<?php
$measurement = $measurement ?? [];
$is_edit = $is_edit ?? false;
?>

<div class="container py-4">
  <form method="post" action="<?= esc_url(admin_url('admin-post.php')) ?>">
    <input type="hidden" name="action" value="save_measurement_form">
    <?php wp_nonce_field('save_measurement', 'measurement_nonce'); ?>

    <?php if ($is_edit && isset($measurement['id'])): ?>
      <input type="hidden" name="id" value="<?= esc_attr($measurement['id']) ?>">
    <?php endif; ?>

    <div class="form-group mb-3">
      <label for="activity_id">Aktywność:</label>
      <select name="activity_id" id="activity_id" class="form-control" required>
        <option value="">-- Wybierz czynność --</option>
        <?php foreach ($activities as $activity): ?>
          <?php
            $name = $activity['act_name'] ?: 'Brak nazwy';
            $hand = $activity['hand'] === 'left' ? 'Lewa' : 'Prawa';
            $ti = $activity['measurement_time_Ti'] ?? '?';
            $label = "{$name} ({$hand} ręka, Ti: {$ti} min)";
          ?>
          <option value="<?= esc_attr($activity['id']) ?>"
            <?= ((isset($measurement['activity_id']) && $measurement['activity_id'] == $activity['id']) ||
                 (isset($_GET['activity_id']) && $_GET['activity_id'] == $activity['id'])) ? 'selected' : '' ?>>
            <?= esc_html($label) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group mb-3">
      <label for="ax">Wartość przyspieszenia X:</label>
      <input type="number" min="0" required step="0.001" name="ax" id="ax" class="form-control" value="<?= esc_attr($measurement['ax'] ?? '') ?>">
    </div>

    <div class="form-group mb-3">
      <label for="ay">Wartość przyspieszenia Y:</label>
      <input type="number" min="0" required step="0.001" name="ay" id="ay" class="form-control" value="<?= esc_attr($measurement['ay'] ?? '') ?>">
    </div>

    <div class="form-group mb-3">
      <label for="az">Wartość przyspieszenia Z:</label>
      <input type="number" min="0" required step="0.001" name="az" id="az" class="form-control" value="<?= esc_attr($measurement['az'] ?? '') ?>">
    </div>

    <input type="submit" class="btn btn-primary" value="<?= $is_edit ? 'Zapisz zmiany' : 'Dodaj pomiar' ?>">
  </form>

  <div class="mt-4">
    <a href="<?= esc_url(site_url('/vibrations')) ?>" class="btn btn-secondary">← Powrót</a>
  </div>
</div>
