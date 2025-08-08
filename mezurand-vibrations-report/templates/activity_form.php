<?php
// $activity — ассоциативный массив с данными (если редактирование)
// $is_edit — true/false (флаг режима редактирования)

$activity = $activity ?? [];
$is_edit = $is_edit ?? false;
?>

<div class="container py-4">
  <form method="post" action="<?= esc_url(admin_url('admin-post.php')) ?>">
    <input type="hidden" name="action" value="save_activity_form">
    <?php wp_nonce_field('save_activity', 'activity_nonce'); ?>

    <?php if ($is_edit && isset($activity['id'])): ?>
      <input type="hidden" name="id" value="<?= esc_attr($activity['id']) ?>">
    <?php endif; ?>

    <div class="form-group mb-3">
      <label for="description_source_measuring">1. Wykonywana czynność / źródło drgań / warunki pomiarów:</label>
      <textarea id="description_source_measuring" required name="description_source_measuring" class="form-control"><?= esc_textarea($activity['description_source_measuring'] ?? '') ?></textarea>
    </div>

    <div class="form-group mb-3">
      <label for="hand">2. Miejsce, orientacja osi oraz metoda mocowania przetwornika:</label>
      <select id="hand" name="hand" class="form-control">
        <option value="left" <?= (isset($activity['hand']) && $activity['hand'] === 'left') ? 'selected' : '' ?>>Lewa ręka</option>
        <option value="right" <?= (isset($activity['hand']) && $activity['hand'] === 'right') ? 'selected' : '' ?>>Prawa ręka</option>
      </select>
    </div>

    <div class="form-group mb-3">
      <label for="act_name">3. Nazwa czynności:</label>
      <input type="text" required name="act_name" id="act_name" class="form-control" value="<?= esc_attr($activity['act_name'] ?? '') ?>">
    </div>

    <div class="form-group mb-3">
      <label for="time_Tp">4. Czas trwania pomiaru Tp [min]:</label>
      <input type="number" required name="time_Tp" step="1" id="time_Tp" class="form-control" value="<?= esc_attr($activity['time_Tp'] ?? 1) ?>" min="1">
    </div>

    <div class="form-group mb-3">
      <label for="measurement_time_Ti">5. Czas ekspozycji Ti [min]:</label>
      <input type="number" required name="measurement_time_Ti" step="1" id="measurement_time_Ti" class="form-control" value="<?= esc_attr($activity['measurement_time_Ti'] ?? 1) ?>" min="1">
    </div>

    <div class="form-group mb-3">
      <label for="b_type_value">6. Wartość niepewności typu B:</label>
      <input type="number" step="0.01" name="b_type_value" id="b_type_value" class="form-control" value="<?= esc_attr($activity['b_type_value'] ?? '') ?>" min="0">
    </div>

    <div class="form-group mb-3">
      <label for="comments">7. Uwagi:</label>
      <textarea id="comments" name="comments" class="form-control"><?= esc_textarea($activity['comments'] ?? '') ?></textarea>
    </div>

    <div class="form-group mb-4">
      <label for="round_up_to">8. Ilość miejsc po przecinku:</label>
      <input type="number" name="round_up_to" id="round_up_to" class="form-control" value="<?= esc_attr($activity['round_up_to'] ?? 1) ?>" min="1" max="10">
    </div>

    <input type="submit" class="btn btn-primary" value="<?= $is_edit ? 'Zapisz zmiany' : 'Zapisz czynność' ?>">
  </form>

  <div class="mt-4">
    <a href="<?= esc_url(site_url('/vibrations')) ?>" class="btn btn-secondary">← Powrót</a>
  </div>
</div>
