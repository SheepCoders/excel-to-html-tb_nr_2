<?php
/**
 * Plugin Name: Mezurand Vibrations Report
 * Description: Dodaje formularz i obliczenia drgań miejscowych.
 * Version: 1.0
 */

if (!defined('ABSPATH')) exit;

define('MEZURAND_REPORT_PATH', plugin_dir_path(__FILE__));

// Подключаем файлы
require_once MEZURAND_REPORT_PATH . 'includes/models/Activity.php';
require_once MEZURAND_REPORT_PATH . 'includes/models/Indicator.php';
require_once MEZURAND_REPORT_PATH . 'includes/models/Measurement.php';
require_once MEZURAND_REPORT_PATH . 'includes/models/Location.php';
require_once MEZURAND_REPORT_PATH . 'includes/models/Obraz.php';
require_once MEZURAND_REPORT_PATH . 'includes/crud/activity_controller.php';
require_once MEZURAND_REPORT_PATH . 'includes/crud/measurement_controller.php';
require_once MEZURAND_REPORT_PATH . 'includes/crud/location_controller.php';
require_once MEZURAND_REPORT_PATH . 'includes/crud/obraz_controller.php';
require_once MEZURAND_REPORT_PATH . 'logic/vibration_logic.php';
require_once MEZURAND_REPORT_PATH . 'logic/constants.php';
require_once MEZURAND_REPORT_PATH . 'logic/activity_calculator.php';
require_once MEZURAND_REPORT_PATH . 'logic/indicator_calculator.php';
require_once MEZURAND_REPORT_PATH . 'logic/obraz_calculator.php';
require_once MEZURAND_REPORT_PATH . 'logic/light_logic.php';


// Регистрируем action
add_action('admin_post_nopriv_save_activity_form', 'handle_activity_form_submission');
add_action('admin_post_save_activity_form', 'handle_activity_form_submission');
add_action('admin_post_nopriv_save_measurement_form', 'handle_measurement_form_submission');
add_action('admin_post_save_measurement_form', 'handle_measurement_form_submission');
add_action('admin_post_delete_activity', 'handle_delete_activity');
add_action('admin_post_delete_measurement', 'handle_delete_measurement');
add_action('admin_post_nopriv_save_location_form', 'handle_location_form_submission');
add_action('admin_post_save_location_form', 'handle_location_form_submission');
add_action('admin_post_nopriv_save_obraz_form', 'handle_obraz_form_submission');
add_action('admin_post_save_obraz_form', 'handle_obraz_form_submission');
add_action('admin_post_delete_location', 'handle_delete_location');
add_action('admin_post_delete_obraz', 'handle_delete_obraz');

// Регистрируем шорткод
add_shortcode('mezurand_vibration_main', 'render_vibration_main');
add_shortcode('mezurand_activity_form', 'render_activity_form');
add_shortcode('mezurand_measurement_form', 'render_measurement_form');
add_shortcode('mezurand_light_main', 'render_light_main');
add_shortcode('mezurand_location_form', 'render_location_form');
add_shortcode('mezurand_obraz_form', 'render_obraz_form');


function render_vibration_main() {
    calculate_and_save_activities();
    calculate_and_save_indicator();

    $activity_list = Activity::get_all();
    $all_indicators = Indicator::get_all();
    $indicator = $all_indicators[0] ?? null;
    ob_start();
    include MEZURAND_REPORT_PATH . 'templates/vibration_main.php';
    return ob_get_clean();
}


function render_activity_form() {
    ob_start();

    // Загружаем данные для редактирования, если передан ?edit=ID
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $activity_id = intval($_GET['edit']);

        $activity_obj = Activity::get_by_id($activity_id);

        if ($activity_obj) {
            // Преобразуем объект в массив
            $activity = (array) $activity_obj;

            // Преобразуем measurements вручную, если нужно:
            $activity['measurements'] = array_map('get_object_vars', $activity_obj->measurements);

            $is_edit = true;
        } else {
            $activity = null;
            $is_edit = false;
        }
    } else {
        $activity = null;
        $is_edit = false;
    }

    include MEZURAND_REPORT_PATH . 'templates/activity_form.php'; // убедись, что путь правильный
    return ob_get_clean();
}


function render_measurement_form() {
    ob_start();

    // Загружаем список объектов Activity всегда
    $activities_obj = Activity::get_all();
    $activities = [];

    if ($activities_obj) {
        foreach ($activities_obj as $activity_obj) {
            $activity = get_object_vars($activity_obj);
            $activities[] = $activity;
        }
    }

    // Теперь обработка редактирования
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $measurement_id = intval($_GET['edit']);
        $measurement_obj = Measurement::get_by_id($measurement_id);

        if ($measurement_obj) {
            $measurement = (array) $measurement_obj;

            if (isset($measurement_obj->activity)) {
                $measurement['activity_id'] = $measurement_obj->activity->id;
            }

            $is_edit = true;
        } else {
            $measurement = null;
            $is_edit = false;
        }
    } else {
        $measurement = null;
        $is_edit = false;
    }
    include MEZURAND_REPORT_PATH . 'templates/measurement_form.php';
    return ob_get_clean();
}


function render_light_main() {
    calculate_and_save_obrazy();

    $location_list = Location::get_all();
    ob_start();
    include MEZURAND_REPORT_PATH . 'templates/light_main.php';
    return ob_get_clean();
}


function render_location_form(){
    ob_start();

    // Загружаем данные для редактирования, если передан ?edit=ID
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $location_id = intval($_GET['edit']);

        $location_obj = Location::get_by_id($location_id);

        if ($location_obj) {
            // Преобразуем объект в массив
            $location = (array) $location_obj;

            // Преобразуем measurements вручную, если нужно:
            $location['obrazy'] = array_map('get_object_vars', $location_obj->obrazy);

            $is_edit = true;
        } else {
            $location = null;
            $is_edit = false;
        }
    } else {
        $location = null;
        $is_edit = false;
    }

    include MEZURAND_REPORT_PATH . 'templates/location_form.php';
    return ob_get_clean();
}


function render_obraz_form(){
    ob_start();

    // Загружаем список объектов Location всегда
    $locations_obj = Location::get_all();
    $locations = [];

    if ($locations_obj) {
        foreach ($locations_obj as $location_obj) {
            $location = get_object_vars($location_obj);
            $locations[] = $location;
        }
    }

    // Теперь обработка редактирования
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $obraz_id = intval($_GET['edit']);
        $obraz_obj = Obraz::get_by_id($obraz_id);

        if ($obraz_obj) {
            $obraz = (array) $obraz_obj;

            if (isset($obraz_obj->location)) {
                $obraz['location_id'] = $obraz_obj->location_id;
            }

            $is_edit = true;
        } else {
            $obraz = null;
            $is_edit = false;
        }
    } else {
        $obraz = null;
        $is_edit = false;
    }
    include MEZURAND_REPORT_PATH . 'templates/obraz_form.php';
    return ob_get_clean();
}


register_activation_hook(__FILE__, 'mezurand_create_tables');

function mezurand_create_tables() {
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $charset_collate = $wpdb->get_charset_collate();
    $prefix = $wpdb->prefix;

    $sql1 = "CREATE TABLE {$prefix}activity (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        description_source_measuring TEXT,
        hand VARCHAR(50) NOT NULL,
        act_name VARCHAR(100),
        time_Tp INT DEFAULT 1,
        measurement_time_Ti INT NOT NULL DEFAULT 1,
        b_type_value FLOAT,
        comments TEXT,
        round_up_to INT NOT NULL DEFAULT 1,
        rounded_ahwx FLOAT,
        rounded_ahwy FLOAT,
        rounded_ahwz FLOAT,
        rounded_vector_summ FLOAT,
        partial_exposure FLOAT,
        vector_summ_time FLOAT,
        ahwx FLOAT,
        ahwy FLOAT,
        ahwz FLOAT,
        vector_summ FLOAT,
        s_axis_x FLOAT,
        s_axis_y FLOAT,
        s_axis_z FLOAT,
        uprobkj_x FLOAT,
        uprobkj_y FLOAT,
        uprobkj_z FLOAT,
        ucj_x FLOAT,
        ucj_y FLOAT,
        ucj_z FLOAT,
        uhvi FLOAT,
        cai FLOAT,
        caiuci2 FLOAT,
        uti_rh FLOAT,
        uti_lh FLOAT,
        cti FLOAT,
        ctiuti2 FLOAT,
        uahv FLOAT,
        PRIMARY KEY (id)
    ) $charset_collate ENGINE=InnoDB;";

    $sql2 = "CREATE TABLE {$prefix}measurement (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        activity_id BIGINT UNSIGNED NOT NULL,
        ax FLOAT NOT NULL,
        ay FLOAT NOT NULL,
        az FLOAT NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (activity_id) REFERENCES {$prefix}activity(id) ON DELETE CASCADE
    ) $charset_collate ENGINE=InnoDB;";

    // Таблица indicator
    $sql3 = "CREATE TABLE {$prefix}indicator (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        daily_exposure_rh TEXT,
        daily_exposure_lh TEXT,
        exposure_30_less_rh TEXT,
        exposure_30_less_lh TEXT,
        multiplicity_NDN_rh FLOAT,
        multiplicity_NDN_lh FLOAT,
        action_threshold_multiplicity_rh FLOAT,
        action_threshold_multiplicity_lh FLOAT,
        multiplicity_pregnant_breastfeeding_rh FLOAT,
        multiplicity_pregnant_breastfeeding_lh FLOAT,
        multiplicity_young_rh FLOAT,
        multiplicity_young_lh FLOAT,
        multiplicity_young_wg_2016_poz_1509_lh FLOAT,
        multiplicity_young_wg_2016_poz_1509_rh FLOAT,
        exposure_time_rh INT,
        exposure_time_lh INT,
        bg20_l FLOAT, bg20_r FLOAT, ba17_l FLOAT, ba17_r FLOAT,
        bm9_l FLOAT, bm9_r FLOAT, bg17_l FLOAT, bg17_r FLOAT,
        bq18_l FLOAT, bq18_r FLOAT, bq17_l FLOAT, bq17_r FLOAT,
        bq15_l FLOAT, bq15_r FLOAT, h19_l FLOAT, h19_r FLOAT,
        bq21_l FLOAT, bq21_r FLOAT, h20_l FLOAT, h20_r FLOAT,
        br18_l FLOAT, br18_r FLOAT, br17_l FLOAT, br17_r FLOAT,
        br15_l FLOAT, br15_r FLOAT, h21_l FLOAT, h21_r FLOAT,
        bs18_l FLOAT, bs18_r FLOAT, bs17_l FLOAT, bs17_r FLOAT,
        bs15_l FLOAT, bs15_r FLOAT, h22_l FLOAT, h22_r FLOAT,
        ucahvmax_l FLOAT, ucahvmax_r FLOAT, uca_8_l FLOAT, uca_8_r FLOAT,
        _2xuca8_l FLOAT, _2xuca8_r FLOAT,
        PRIMARY KEY (id)
    ) $charset_collate ENGINE=InnoDB;";

    // Таблица location
    $sql4 = "CREATE TABLE {$prefix}location (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        description_location TEXT,
        description_light TEXT,
        uwagi TEXT,
        luks_before TEXT,
        luks_after TEXT,
        legend TEXT,
        round_up_to INT NOT NULL DEFAULT 1,
        U FLOAT,
        PRIMARY KEY (id),
    ) $charset_collate ENGINE=InnoDB;";

    // Таблица obraz
    $sql5 = "CREATE TABLE {$prefix}obraz (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        location_id BIGINT UNSIGNED NOT NULL,
        obraz_plane VARCHAR(50) NOT NULL,
        average_illuminance TEXT,
        illumination_uniformity TEXT,
        normative_illuminance TEXT,
        normative_illumination_uniformity TEXT,
        reading_1 FLOAT NOT NULL,
        reading_2 FLOAT, reading_3 FLOAT, reading_4 FLOAT, reading_5 FLOAT,
        reading_6 FLOAT, reading_7 FLOAT, reading_8 FLOAT, reading_9 FLOAT,
        reading_10 FLOAT, reading_11 FLOAT, reading_12 FLOAT, reading_13 FLOAT,
        reading_14 FLOAT, reading_15 FLOAT, reading_16 FLOAT, reading_17 FLOAT,
        reading_18 FLOAT, reading_19 FLOAT, reading_20 FLOAT, reading_21 FLOAT,
        reading_22 FLOAT, reading_23 FLOAT, reading_24 FLOAT, reading_25 FLOAT,
        reading_26 FLOAT, reading_27 FLOAT, reading_28 FLOAT, reading_29 FLOAT,
        reading_30 FLOAT,
        PRIMARY KEY (id),
        FOREIGN KEY (location_id) REFERENCES {$prefix}location(id) ON DELETE CASCADE
    ) $charset_collate ENGINE=InnoDB;";

    dbDelta($sql1);
    dbDelta($sql2);
    dbDelta($sql3);
    dbDelta($sql4);
    dbDelta($sql5);
}


register_activation_hook(__FILE__, 'mezurand_create_pages');

function mezurand_create_pages() {
    $pages = [
        'vibrations' => '[mezurand_vibration_main]',
        'activity' => '[mezurand_activity_form]',
        'measurement' => '[mezurand_measurement_form]',
        'light' => '[mezurand_light_main]',
        'location' => '[mezurand_location_form]',
        'obraz' => '[mezurand_obraz_form]',
    ];

    foreach ($pages as $slug => $shortcode) {
        if (!get_page_by_path($slug)) {
            wp_insert_post([
                'post_title' => ucfirst(str_replace('-', ' ', $slug)),
                'post_name' => $slug,
                'post_content' => $shortcode,
                'post_status' => 'publish',
                'post_type' => 'page',
            ]);
        }
    }
}
