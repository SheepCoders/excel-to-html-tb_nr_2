<?php

class Indicator
{
    public ?int $id = null;

    // Tekstowe pola
    public ?string $daily_exposure_rh = null;
    public ?string $daily_exposure_lh = null;
    public ?string $exposure_30_less_rh = null;
    public ?string $exposure_30_less_lh = null;

    // Liczbowe (float) pola
    public ?float $multiplicity_NDN_rh = null;
    public ?float $multiplicity_NDN_lh = null;
    public ?float $action_threshold_multiplicity_rh = null;
    public ?float $action_threshold_multiplicity_lh = null;
    public ?float $multiplicity_pregnant_breastfeeding_rh = null;
    public ?float $multiplicity_pregnant_breastfeeding_lh = null;
    public ?float $multiplicity_young_rh = null;
    public ?float $multiplicity_young_lh = null;
    public ?float $multiplicity_young_wg_2016_poz_1509_lh = null;
    public ?float $multiplicity_young_wg_2016_poz_1509_rh = null;

    // Debug — czas ekspozycji
    public ?int $exposure_time_rh = null;
    public ?int $exposure_time_lh = null;

    // Wskaźniki (debug)
    public ?float $bg20_l = null;
    public ?float $bg20_r = null;
    public ?float $ba17_l = null;
    public ?float $ba17_r = null;
    public ?float $bm9_l = null;
    public ?float $bm9_r = null;
    public ?float $bg17_l = null;
    public ?float $bg17_r = null;
    public ?float $bq18_l = null;
    public ?float $bq18_r = null;
    public ?float $bq17_l = null;
    public ?float $bq17_r = null;
    public ?float $bq15_l = null;
    public ?float $bq15_r = null;
    public ?float $h19_l = null;
    public ?float $h19_r = null;
    public ?float $bq21_l = null;
    public ?float $bq21_r = null;
    public ?float $h20_l = null;
    public ?float $h20_r = null;
    public ?float $br18_l = null;
    public ?float $br18_r = null;
    public ?float $br17_l = null;
    public ?float $br17_r = null;
    public ?float $br15_l = null;
    public ?float $br15_r = null;
    public ?float $h21_l = null;
    public ?float $h21_r = null;
    public ?float $bs18_l = null;
    public ?float $bs18_r = null;
    public ?float $bs17_l = null;
    public ?float $bs17_r = null;
    public ?float $bs15_l = null;
    public ?float $bs15_r = null;
    public ?float $h22_l = null;
    public ?float $h22_r = null;

    // Niepewności
    public ?float $ucahvmax_l = null;
    public ?float $ucahvmax_r = null;
    public ?float $uca_8_l = null;
    public ?float $uca_8_r = null;
    public ?float $_2xuca8_l = null;
    public ?float $_2xuca8_r = null;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }


    public function to_array(): array
    {
        $props = get_object_vars($this);
        unset($props['id']); // id в запросе отдельно
        return $props;
    }

    public function save(): bool
    {
        global $wpdb;
        $table = $wpdb->prefix . 'indicator';
        $data = $this->to_array();

        if ($this->id !== null && $this->exists_in_db()) {
            // UPDATE
            $result = $wpdb->update($table, $data, ['id' => $this->id]);
            return $result !== false;
        } else {
            // INSERT
            $result = $wpdb->insert($table, $data);
            if ($result !== false) {
                $this->id = $wpdb->insert_id;
                return true;
            }
            return false;
        }
    }

    public function exists_in_db(): bool
    {
        if ($this->id === null) {
            return false;
        }
        global $wpdb;
        $table = $wpdb->prefix . 'indicator';
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE id = %d", $this->id));
        return ($count > 0);
    }

    public static function get_by_id(int $id): ?self
    {
        global $wpdb;
        $table = $wpdb->prefix . 'indicator';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id), ARRAY_A);
        if ($row) {
            return new self($row);
        }
        return null;
    }

    public static function get_all(): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'indicator';
        $results = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC", ARRAY_A);

        $indicators = [];
        foreach ($results as $row) {
            $indicators[] = new self($row);
        }
        return $indicators;
    }

    public function delete(): bool
    {
        if ($this->id === null) {
            return false;
        }
        global $wpdb;
        $table = $wpdb->prefix . 'indicator';
        $result = $wpdb->delete($table, ['id' => $this->id]);
        if ($result !== false) {
            $this->id = null;
            return true;
        }
        return false;
    }
}
