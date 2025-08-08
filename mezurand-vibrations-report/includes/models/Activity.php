<?php

class Activity
{
    public ?int $id = null;

    // HAND_CHOICES
    public const HAND_LEFT = 'left';
    public const HAND_RIGHT = 'right';

    // Основные поля
    public ?string $description_source_measuring = null;
    public string $hand = self::HAND_LEFT;  // 'left' или 'right'
    public ?string $act_name = null;
    public ?int $time_Tp = 1;
    public int $measurement_time_Ti = 1;
    public ?float $b_type_value = null;
    public ?string $comments = null;
    public int $round_up_to = 1;

    // Результаты расчётов
    public ?float $rounded_ahwx = null;
    public ?float $rounded_ahwy = null;
    public ?float $rounded_ahwz = null;
    public ?float $rounded_vector_summ = null;
    public ?float $partial_exposure = null;
    public ?float $vector_summ_time = null;

    // debug / raw
    public ?float $ahwx = null;
    public ?float $ahwy = null;
    public ?float $ahwz = null;
    public ?float $vector_summ = null;
    public ?float $s_axis_x = null;
    public ?float $s_axis_y = null;
    public ?float $s_axis_z = null;
    public ?float $uprobkj_x = null;
    public ?float $uprobkj_y = null;
    public ?float $uprobkj_z = null;
    public ?float $ucj_x = null;
    public ?float $ucj_y = null;
    public ?float $ucj_z = null;
    public ?float $uhvi = null;
    public ?float $cai = null;
    public ?float $caiuci2 = null;
    public ?float $uti_rh = null;
    public ?float $uti_lh = null;
    public ?float $cti = null;
    public ?float $ctiuti2 = null;
    public ?float $uahv = null;

    /** @var Measurement[] */
    public array $measurements = [];

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }


    public function save(): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'activity';

        $data = [
            'description_source_measuring' => $this->description_source_measuring,
            'hand' => $this->hand,
            'act_name' => $this->act_name,
            'time_Tp' => $this->time_Tp,
            'measurement_time_Ti' => $this->measurement_time_Ti,
            'b_type_value' => $this->b_type_value,
            'comments' => $this->comments,
            'round_up_to' => $this->round_up_to,

            'rounded_ahwx' => $this->rounded_ahwx,
            'rounded_ahwy' => $this->rounded_ahwy,
            'rounded_ahwz' => $this->rounded_ahwz,
            'rounded_vector_summ' => $this->rounded_vector_summ,
            'partial_exposure' => $this->partial_exposure,
            'vector_summ_time' => $this->vector_summ_time,

            'ahwx' => $this->ahwx,
            'ahwy' => $this->ahwy,
            'ahwz' => $this->ahwz,
            'vector_summ' => $this->vector_summ,
            's_axis_x' => $this->s_axis_x,
            's_axis_y' => $this->s_axis_y,
            's_axis_z' => $this->s_axis_z,
            'uprobkj_x' => $this->uprobkj_x,
            'uprobkj_y' => $this->uprobkj_y,
            'uprobkj_z' => $this->uprobkj_z,
            'ucj_x' => $this->ucj_x,
            'ucj_y' => $this->ucj_y,
            'ucj_z' => $this->ucj_z,
            'uhvi' => $this->uhvi,
            'cai' => $this->cai,
            'caiuci2' => $this->caiuci2,
            'uti_rh' => $this->uti_rh,
            'uti_lh' => $this->uti_lh,
            'cti' => $this->cti,
            'ctiuti2' => $this->ctiuti2,
            'uahv' => $this->uahv,
        ];

        if ($this->id !== null) {
            $updated = $wpdb->update($table, $data, ['id' => $this->id]);
            return $updated !== false;
        } else {
            $inserted = $wpdb->insert($table, $data);
            if ($inserted !== false) {
                $this->id = $wpdb->insert_id;
                return true;
            }
            return false;
        }
    }


    private function exists_in_db(): bool {
        if ($this->id === null) return false;

        global $wpdb;
        return (bool) $wpdb->get_var(
            $wpdb->prepare("SELECT 1 FROM {$wpdb->prefix}activity WHERE id = %d", $this->id)
        );
    }


    public static function get_all(): array {
        global $wpdb;
        $table = $wpdb->prefix . 'activity';

        $results = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");

        $activities = [];
        foreach ($results as $row) {
            $activity = new Activity((array)$row);
            $activity->measurements = Measurement::get_by_activity_id($row->id);
            $activities[] = $activity;
        }
        return $activities;
    }


    public static function get_by_id(int $id): ?Activity {
        global $wpdb;
        $table = $wpdb->prefix . 'activity';

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
        if ($row) {
            $activity = new self((array)$row);
            $activity->measurements = Measurement::get_by_activity_id($row->id);
            return $activity;
        }
        return null;
    }


    public static function get_by_measurement_id(int $measurement_id): ?Activity
    {
        global $wpdb;
        $measurement_table = $wpdb->prefix . 'measurement';

        // Получаем activity_id для данного measurement_id
        $activity_id = $wpdb->get_var(
            $wpdb->prepare("SELECT activity_id FROM $measurement_table WHERE id = %d", $measurement_id)
        );

        if ($activity_id === null) {
            return null; // Измерение не найдено
        }

        // Возвращаем Activity по найденному activity_id
        return self::get_by_id((int)$activity_id);
    }


    public static function delete(int $id): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'activity';
        return (bool) $wpdb->delete($table, ['id' => $id]);
    }


    public function __toString(): string
    {
        return "{$this->description_source_measuring}, {$this->hand} / {$this->measurement_time_Ti} min.";
    }
}
