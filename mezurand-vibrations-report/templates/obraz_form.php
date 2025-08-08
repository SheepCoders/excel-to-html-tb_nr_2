<?php
$obraz = $obraz ?? [];
$is_edit = $is_edit ?? false;
?>

<div class="container py-4">
  <form method="post" action="<?= esc_url(admin_url('admin-post.php')) ?>">
    <input type="hidden" name="action" value="save_obraz_form">
    <?php wp_nonce_field('save_obraz', 'obraz_nonce'); ?>

    <?php if ($is_edit && isset($obraz['id'])): ?>
      <input type="hidden" name="id" value="<?= esc_attr($obraz['id']) ?>">
    <?php endif; ?>

    <div class="form-group mb-3">
      <label for="location_id">1. Lokalizacja :</label>
      <select name="location_id" id="location_id" class="form-control" required>
        <option value="">-- Wybierz lokalizacja --</option>
        <?php foreach ($locations as $location): ?>
          <?php
            $description_location = $location['description_location'] ?: 'Brak opisu';
            $description_light = $location['description_light'] ?: 'Brak opisu';
            $label = "{$description_location} / {$description_light}";
          ?>
          <option value="<?= esc_attr($location['id']) ?>"
            <?= ((isset($obraz['location_id']) && $obraz['location_id'] == $location['id']) ||
                 (isset($_GET['location_id']) && $_GET['location_id'] == $location['id'])) ? 'selected' : '' ?>>
            <?= esc_html($label) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group mb-3">
      <label for="obraz_plane">2. Płaszczyzna pomiarowa:</label>
      <select id="obraz_plane" name="obraz_plane" class="form-control">
        <option value="Pole/obszar zadania" <?= (isset($obraz['obraz_plane']) && $obraz['obraz_plane'] === 'Pole/obszar zadania') ? 'selected' : '' ?>>Pole/obszar zadania</option>
        <option value="Pole/obszar otoczenia" <?= (isset($obraz['obraz_plane']) && $obraz['obraz_plane'] === 'Pole/obszar otoczenia') ? 'selected' : '' ?>>Pole/obszar otoczenia</option>
        <option value="Pole/obszar tla" <?= (isset($obraz['obraz_plane']) && $obraz['obraz_plane'] === 'Pole/obszar tla') ? 'selected' : '' ?>>Pole/obszar tla</option>
      </select>
    </div>

    <div class="form-group mb-3">
      <label for="reading_1">3. Odczyty jednostkowe natężenia oświetlenia [lx]:</label>
      <input type="number" step="0.01" min="0" required name="reading_1" id="reading_1" class="form-control" value="<?= esc_attr($obraz['reading_1'] ?? '') ?>">
    </div>

    <?php for ($i = 2; $i <= 30; $i++): ?>
        <div class="form-group mb-3">
            <label for="reading_<?= $i ?>"><?= $i + 2 ?>. Odczyty jednostkowe natężenia oświetlenia [lx]:</label>
            <input type="number" step="0.01" name="reading_<?= $i ?>" id="reading_<?= $i ?>"
                   class="form-control" value="<?= esc_attr($obraz['reading_' . $i] ?? '') ?>" min="0">
        </div>
    <?php endfor; ?>



    <input type="submit" class="btn btn-primary" value="<?= $is_edit ? 'Zapisz zmiany' : 'Dodaj оdczyt' ?>">
  </form>

  <div class="mt-4">
    <a href="<?= esc_url(site_url('/light')) ?>" class="btn btn-secondary">← Powrót</a>
  </div>
</div>
