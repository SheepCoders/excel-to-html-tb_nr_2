<?php

function handle_obraz_form_submission() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

    if (!isset($_POST['obraz_nonce']) || !wp_verify_nonce($_POST['obraz_nonce'], 'save_obraz')) {
        wp_die('Błąd bezpieczeństwa (nonce).');
    }

    try {
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        $obraz_plane = in_array($_POST['obraz_plane'] ?? '', ['Pole/obszar zadania', 'Pole/obszar otoczenia', 'Pole/obszar tla']) ? $_POST['obraz_plane'] : 'Pole/obszar zadania';
        $reading_1 = floatval($_POST['reading_1']);
        $reading_2 = floatval($_POST['reading_2']);
        $reading_3 = floatval($_POST['reading_3']);
        $reading_4 = floatval($_POST['reading_4']);
        $reading_5 = floatval($_POST['reading_5']);
        $reading_6 = floatval($_POST['reading_6']);
        $reading_7 = floatval($_POST['reading_7']);
        $reading_8 = floatval($_POST['reading_8']);
        $reading_9 = floatval($_POST['reading_9']);
        $reading_10 = floatval($_POST['reading_10']);
        $reading_11 = floatval($_POST['reading_11']);
        $reading_12 = floatval($_POST['reading_12']);
        $reading_13 = floatval($_POST['reading_13']);
        $reading_14 = floatval($_POST['reading_14']);
        $reading_15 = floatval($_POST['reading_15']);
        $reading_16 = floatval($_POST['reading_16']);
        $reading_17 = floatval($_POST['reading_17']);
        $reading_18 = floatval($_POST['reading_18']);
        $reading_19 = floatval($_POST['reading_19']);
        $reading_20 = floatval($_POST['reading_20']);
        $reading_21 = floatval($_POST['reading_21']);
        $reading_22 = floatval($_POST['reading_22']);
        $reading_23 = floatval($_POST['reading_23']);
        $reading_24 = floatval($_POST['reading_24']);
        $reading_25 = floatval($_POST['reading_25']);
        $reading_26 = floatval($_POST['reading_26']);
        $reading_27 = floatval($_POST['reading_27']);
        $reading_28 = floatval($_POST['reading_28']);
        $reading_29 = floatval($_POST['reading_29']);
        $reading_30 = floatval($_POST['reading_30']);
        $location_id = intval($_POST['location_id']);

        // ❗ Валидация срабатывает в конструкторе
        $obraz = new Obraz(
        $obraz_plane,
        $average_illuminance = null,
        $illumination_uniformity = null,
        $normative_illuminance = null,
        $normative_illumination_uniformity = null,
        $reading_1,
        $reading_2,
        $reading_3,
        $reading_4,
        $reading_5,
        $reading_6,
        $reading_7,
        $reading_8,
        $reading_9,
        $reading_10,
        $reading_11,
        $reading_12,
        $reading_13,
        $reading_14,
        $reading_15,
        $reading_16,
        $reading_17,
        $reading_18,
        $reading_19,
        $reading_20,
        $reading_21,
        $reading_22,
        $reading_23,
        $reading_24,
        $reading_25,
        $reading_26,
        $reading_27,
        $reading_28,
        $reading_29,
        $reading_30,
        $id,
        );
        $obraz->id = $id;
        $obraz->location_id = $location_id;
        $obraz->save($location_id);

        wp_redirect(site_url('/light'));
        exit;

    } catch (InvalidArgumentException $e) {
        wp_die("Błąd walidacji: " . $e->getMessage());
    } catch (Exception $e) {
        wp_die("Nieoczekiwany błąd: " . $e->getMessage());
    }
}


function handle_delete_obraz() {
    if (
        !isset($_POST['delete_obraz_nonce'], $_POST['id']) ||
        !wp_verify_nonce($_POST['delete_obraz_nonce'], 'delete_obraz_' . $_POST['id'])
    ) {
        wp_die('Błąd weryfikacji nonce.');
    }

    $id = intval($_POST['id']);

    if ($id > 0) {
        Obraz::delete($id);
    }

    wp_redirect(site_url('/light'));
    exit;
}
