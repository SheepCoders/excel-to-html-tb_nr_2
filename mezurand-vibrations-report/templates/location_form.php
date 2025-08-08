<?php
// $location — ассоциативный массив с данными (если редактирование)
// $is_edit — true/false (флаг режима редактирования)

$location = $location ?? [];
$is_edit = $is_edit ?? false;
?>

<div class="container py-4">
  <form method="post" action="<?= esc_url(admin_url('admin-post.php')) ?>">
    <input type="hidden" name="action" value="save_location_form">
    <?php wp_nonce_field('save_location', 'location_nonce'); ?>

    <?php if ($is_edit && isset($location['id'])): ?>
      <input type="hidden" name="id" value="<?= esc_attr($location['id']) ?>">
    <?php endif; ?>

    <div class="form-group mb-3">
      <label for="description_location">1. Lokalizacja stanowiska pracy:</label>
      <textarea id="description_location" required name="description_location" class="form-control"><?= esc_textarea($location['description_location'] ?? '') ?></textarea>
    </div>

    <div class="form-group mb-3">
      <label for="description_light">2. Rodzaj/ilość oświetlenia:</label>
      <input type="text" required name="description_light" id="description_light" class="form-control" value="<?= esc_attr($location['description_light'] ?? '') ?>">
    </div>

    <div class="form-group mb-4">
      <label for="round_up_to">3. Ilość miejsc po przecinku:</label>
      <input type="number" name="round_up_to" id="round_up_to" class="form-control" value="<?= esc_attr($location['round_up_to'] ?? 1) ?>" min="1" max="10">
    </div>

    <div class="form-group mb-4">
      <label for="U">4. U - niepewność :</label>
      <input type="number" step="0.01" name="U" id="U" class="form-control" value="<?= esc_attr($location['U'] ?? 1) ?>" min="0.1">
    </div>

    <div class="form-group mb-3">
      <label for="uwagi">5. Uwagi:</label>
      <textarea id="uwagi" name="uwagi" class="form-control"><?= esc_textarea($location['uwagi'] ?? '') ?></textarea>
    </div>

    <div class="form-group mb-3">
      <label for="luks_before">6. Wynik sprawdzenia luksomierza przed serią pomiarów [lx]:</label>
      <textarea id="luks_before" name="luks_before" class="form-control"><?= esc_textarea($location['luks_before'] ?? '') ?></textarea>
    </div>

    <div class="form-group mb-3">
      <label for="luks_after">7. Wynik sprawdzenia luksomierza po serii pomiarów [lx]:</label>
      <textarea id="luks_after" name="luks_after" class="form-control"><?= esc_textarea($location['luks_after'] ?? '') ?></textarea>
    </div>

    <div class="form-group mb-3">
      <label for="legend">8. Legenda do opisu oświetlenia stanowiska pracy:</label>
      <textarea id="legend" name="legend" class="form-control"><?= esc_textarea($location['legend'] ?? '') ?></textarea>
    </div>

    <input type="submit" class="btn btn-primary" value="<?= $is_edit ? 'Zapisz zmiany' : 'Zapisz lokalizacja' ?>">
  </form>

  <div class="mt-4">
    <a href="<?= esc_url(site_url('/light')) ?>" class="btn btn-secondary">← Powrót</a>
  </div>
</div>
