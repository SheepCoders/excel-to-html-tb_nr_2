<?php

function handle_activity_form_submission() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

    if (!isset($_POST['activity_nonce']) || !wp_verify_nonce($_POST['activity_nonce'], 'save_activity')) {
        wp_die('Błąd bezpieczeństwa (nonce).');
    }

    define('MEZURAND_REPORT_PATH', plugin_dir_path(__FILE__));

    // Подключаем файлы
    require_once MEZURAND_REPORT_PATH . 'includes/models/Activity.php';

    // HTML + PHP валидация и очистка
    $data = [
        'id' => !empty($_POST['id']) ? intval($_POST['id']) : null,
        'description_source_measuring' => sanitize_textarea_field($_POST['description_source_measuring'] ?? ''),
        'hand' => in_array($_POST['hand'] ?? '', ['left', 'right']) ? $_POST['hand'] : 'left',
        'act_name' => sanitize_text_field($_POST['act_name'] ?? ''),
        'time_Tp' => max(1, intval($_POST['time_Tp'] ?? 1)),
        'measurement_time_Ti' => max(1, intval($_POST['measurement_time_Ti'] ?? 1)),
        'b_type_value' => isset($_POST['b_type_value']) ? floatval($_POST['b_type_value']) : null,
        'comments' => sanitize_textarea_field($_POST['comments'] ?? ''),
        'round_up_to' => min(10, max(1, intval($_POST['round_up_to'] ?? 1))),
    ];

    $activity = new Activity($data);
    $activity->save();

    wp_redirect(site_url('/vibrations'));
    exit;
}


function handle_delete_activity() {
    if (
        !isset($_POST['delete_activity_nonce'], $_POST['id']) ||
        !wp_verify_nonce($_POST['delete_activity_nonce'], 'delete_activity_' . $_POST['id'])
    ) {
        wp_die('Błąd weryfikacji nonce.');
    }

    $id = intval($_POST['id']);

    if ($id > 0) {
        Activity::delete($id);
    }

    wp_redirect(site_url('/vibrations'));
    exit;
}