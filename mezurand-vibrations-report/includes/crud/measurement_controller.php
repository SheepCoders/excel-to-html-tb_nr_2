<?php

function handle_measurement_form_submission() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

    if (!isset($_POST['measurement_nonce']) || !wp_verify_nonce($_POST['measurement_nonce'], 'save_measurement')) {
        wp_die('Błąd bezpieczeństwa (nonce).');
    }

    try {
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        $ax = floatval($_POST['ax']);
        $ay = floatval($_POST['ay']);
        $az = floatval($_POST['az']);
        $activity_id = intval($_POST['activity_id']);

        // ❗ Валидация срабатывает в конструкторе
        $measurement = new Measurement($ax, $ay, $az, $id);
        $measurement->save($activity_id);

        wp_redirect(site_url('/vibrations'));
        exit;

    } catch (InvalidArgumentException $e) {
        wp_die("Błąd walidacji: " . $e->getMessage());
    } catch (Exception $e) {
        wp_die("Nieoczekiwany błąd: " . $e->getMessage());
    }
}


function handle_delete_measurement() {
    if (
        !isset($_POST['delete_measurement_nonce'], $_POST['id']) ||
        !wp_verify_nonce($_POST['delete_measurement_nonce'], 'delete_measurement_' . $_POST['id'])
    ) {
        wp_die('Błąd weryfikacji nonce.');
    }

    $id = intval($_POST['id']);

    if ($id > 0) {
        Measurement::delete($id);
    }

    wp_redirect(site_url('/vibrations'));
    exit;
}
