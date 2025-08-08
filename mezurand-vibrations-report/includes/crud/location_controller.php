<?php

function handle_location_form_submission() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

    if (!isset($_POST['location_nonce']) || !wp_verify_nonce($_POST['location_nonce'], 'save_location')) {
        wp_die('Błąd bezpieczeństwa (nonce).');
    }

    define('MEZURAND_REPORT_PATH', plugin_dir_path(__FILE__));

    // Подключаем файлы
    require_once MEZURAND_REPORT_PATH . 'includes/models/Location.php';

    // HTML + PHP валидация и очистка
    $data = [
        'id' => !empty($_POST['id']) ? intval($_POST['id']) : null,
        'description_location' => sanitize_textarea_field($_POST['description_location'] ?? ''),
        'description_light' => sanitize_text_field($_POST['description_light'] ?? ''),
        'round_up_to' => min(10, max(1, intval($_POST['round_up_to'] ?? 1))),
        'U' => isset($_POST['U']) ? floatval($_POST['U']) : null,
        'uwagi' => sanitize_textarea_field($_POST['uwagi'] ?? ''),
        'luks_before' => sanitize_textarea_field($_POST['luks_before'] ?? ''),
        'luks_after' => sanitize_textarea_field($_POST['luks_after'] ?? ''),
        'legend' => sanitize_textarea_field($_POST['legend'] ?? ''),
    ];

    $location = new Location($data);
    $location->save();

    wp_redirect(site_url('/light'));
    exit;
}


function handle_delete_location() {
    if (
        !isset($_POST['delete_location_nonce'], $_POST['id']) ||
        !wp_verify_nonce($_POST['delete_location_nonce'], 'delete_location_' . $_POST['id'])
    ) {
        wp_die('Błąd weryfikacji nonce.');
    }

    $id = intval($_POST['id']);

    if ($id > 0) {
        Location::delete($id);
    }

    wp_redirect(site_url('/light'));
    exit;
}
