<?php

class Obraz
{
    public ?int $id = null;

    // OBRAZ PLANE
    public ?int $location_id = null;
    public const ZADANIA = 'Pole/obszar zadania';
    public const OTOCZENIA = 'Pole/obszar otoczenia';
    public const TLA = 'Pole/obszar tla';

    public string $obraz_plane = self::ZADANIA;
    public ?string $average_illuminance = null;
    public ?string $illumination_uniformity = null;
    public ?string $normative_illuminance = null;
    public ?string $normative_illumination_uniformity = null;

    // Individual readings
    public float $reading_1;
    public ?float $reading_2 = null;
    public ?float $reading_3 = null;
    public ?float $reading_4 = null;
    public ?float $reading_5 = null;
    public ?float $reading_6 = null;
    public ?float $reading_7 = null;
    public ?float $reading_8 = null;
    public ?float $reading_9 = null;
    public ?float $reading_10 = null;
    public ?float $reading_11 = null;
    public ?float $reading_12 = null;
    public ?float $reading_13 = null;
    public ?float $reading_14 = null;
    public ?float $reading_15 = null;
    public ?float $reading_16 = null;
    public ?float $reading_17 = null;
    public ?float $reading_18 = null;
    public ?float $reading_19 = null;
    public ?float $reading_20 = null;
    public ?float $reading_21 = null;
    public ?float $reading_22 = null;
    public ?float $reading_23 = null;
    public ?float $reading_24 = null;
    public ?float $reading_25 = null;
    public ?float $reading_26 = null;
    public ?float $reading_27 = null;
    public ?float $reading_28 = null;
    public ?float $reading_29 = null;
    public ?float $reading_30 = null;

    public ?Location $location = null;

    public function save(int $location_id): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'obraz';

        $data = [

            'location_id' => $location_id,
            'obraz_plane' => $this->obraz_plane,
            'average_illuminance' => $this->average_illuminance,
            'illumination_uniformity' => $this->illumination_uniformity,
            'normative_illuminance' => $this->normative_illuminance,
            'normative_illumination_uniformity' => $this->normative_illumination_uniformity,
            'reading_1' => $this->reading_1,
            'reading_2' => $this->reading_2,
            'reading_3' => $this->reading_3,
            'reading_4' => $this->reading_4,
            'reading_5' => $this->reading_5,
            'reading_6' => $this->reading_6,
            'reading_7' => $this->reading_7,
            'reading_8' => $this->reading_8,
            'reading_9' => $this->reading_9,
            'reading_10' => $this->reading_10,
            'reading_11' => $this->reading_11,
            'reading_12' => $this->reading_12,
            'reading_13' => $this->reading_13,
            'reading_14' => $this->reading_14,
            'reading_15' => $this->reading_15,
            'reading_16' => $this->reading_16,
            'reading_17' => $this->reading_17,
            'reading_18' => $this->reading_18,
            'reading_19' => $this->reading_19,
            'reading_20' => $this->reading_20,
            'reading_21' => $this->reading_21,
            'reading_22' => $this->reading_22,
            'reading_23' => $this->reading_23,
            'reading_24' => $this->reading_24,
            'reading_25' => $this->reading_25,
            'reading_26' => $this->reading_26,
            'reading_27' => $this->reading_27,
            'reading_28' => $this->reading_28,
            'reading_29' => $this->reading_29,
            'reading_30' => $this->reading_30,
        ];

        // Если есть ID - обновляем, иначе создаем новую запись
        if ($this->id !== null) {
            if (!$this->exists_in_db()) {
                throw new RuntimeException("Obraz with ID {$this->id} does not exist");
            }
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
            $wpdb->prepare("SELECT 1 FROM {$wpdb->prefix}obraz WHERE id = %d", $this->id)
        );
    }


    public static function get_all(): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'obraz';

        $results = $wpdb->get_results("SELECT * FROM $table");

        $obrazy = [];
        foreach ($results as $row) {
            $obrazy[] = new Obraz(
                (string)$row->obraz_plane,
                (string)$row->average_illuminance,
                (string)$row->illumination_uniformity,
                (string)$row->normative_illuminance,
                (string)$row->normative_illumination_uniformity,
                (float)$row->reading_1,
                (float)$row->reading_2,
                (float)$row->reading_3,
                (float)$row->reading_4,
                (float)$row->reading_5,
                (float)$row->reading_6,
                (float)$row->reading_7,
                (float)$row->reading_8,
                (float)$row->reading_9,
                (float)$row->reading_10,
                (float)$row->reading_11,
                (float)$row->reading_12,
                (float)$row->reading_13,
                (float)$row->reading_14,
                (float)$row->reading_15,
                (float)$row->reading_16,
                (float)$row->reading_17,
                (float)$row->reading_18,
                (float)$row->reading_19,
                (float)$row->reading_20,
                (float)$row->reading_21,
                (float)$row->reading_22,
                (float)$row->reading_23,
                (float)$row->reading_24,
                (float)$row->reading_25,
                (float)$row->reading_26,
                (float)$row->reading_27,
                (float)$row->reading_28,
                (float)$row->reading_29,
                (float)$row->reading_30,
                (int)$row->location_id,

                (int)$row->id
            );
        }
        return $obrazy;
    }


    public static function get_by_id(int $id): ?Obraz
    {
        global $wpdb;
        $table = $wpdb->prefix . 'obraz';

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));

        if ($row) {
            $location = null;
            if (!empty($row->location_id)) {
                $location = Location::get_by_id((int)$row->location_id);
            }

            return new Obraz(
                (string)$row->obraz_plane,
                (string)$row->average_illuminance,
                (string)$row->illumination_uniformity,
                (string)$row->normative_illuminance,
                (string)$row->normative_illumination_uniformity,
                (float)$row->reading_1,
                (float)$row->reading_2,
                (float)$row->reading_3,
                (float)$row->reading_4,
                (float)$row->reading_5,
                (float)$row->reading_6,
                (float)$row->reading_7,
                (float)$row->reading_8,
                (float)$row->reading_9,
                (float)$row->reading_10,
                (float)$row->reading_11,
                (float)$row->reading_12,
                (float)$row->reading_13,
                (float)$row->reading_14,
                (float)$row->reading_15,
                (float)$row->reading_16,
                (float)$row->reading_17,
                (float)$row->reading_18,
                (float)$row->reading_19,
                (float)$row->reading_20,
                (float)$row->reading_21,
                (float)$row->reading_22,
                (float)$row->reading_23,
                (float)$row->reading_24,
                (float)$row->reading_25,
                (float)$row->reading_26,
                (float)$row->reading_27,
                (float)$row->reading_28,
                (float)$row->reading_29,
                (float)$row->reading_30,
                (int)$row->location_id,

                (int)$row->id,

                $location,
            );
        }

        return null;
    }


    public static function get_by_location_id(int $location_id): array {
        global $wpdb;
        $table = $wpdb->prefix . 'obraz';
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE location_id = %d", $location_id
        ));

        $obrazy = [];
        foreach ($results as $row) {
            $obrazy[] = new Obraz(
                (string)$row->obraz_plane,
                (string)$row->average_illuminance,
                (string)$row->illumination_uniformity,
                (string)$row->normative_illuminance,
                (string)$row->normative_illumination_uniformity,
                (float)$row->reading_1,
                (float)$row->reading_2,
                (float)$row->reading_3,
                (float)$row->reading_4,
                (float)$row->reading_5,
                (float)$row->reading_6,
                (float)$row->reading_7,
                (float)$row->reading_8,
                (float)$row->reading_9,
                (float)$row->reading_10,
                (float)$row->reading_11,
                (float)$row->reading_12,
                (float)$row->reading_13,
                (float)$row->reading_14,
                (float)$row->reading_15,
                (float)$row->reading_16,
                (float)$row->reading_17,
                (float)$row->reading_18,
                (float)$row->reading_19,
                (float)$row->reading_20,
                (float)$row->reading_21,
                (float)$row->reading_22,
                (float)$row->reading_23,
                (float)$row->reading_24,
                (float)$row->reading_25,
                (float)$row->reading_26,
                (float)$row->reading_27,
                (float)$row->reading_28,
                (float)$row->reading_29,
                (float)$row->reading_30,
                (int)$row->location_id,

                (int)$row->id,

                null,      // Location можно установить позже
            );
        }
        return $obrazy;
    }


    public static function delete(int $id): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'obraz';
        return (bool) $wpdb->delete($table, ['id' => $id]);
    }


    public function __construct(
    string $obraz_plane,
    ?string $average_illuminance = null,
    ?string $illumination_uniformity = null,
    ?string $normative_illuminance = null,
    ?string $normative_illumination_uniformity = null,
    float $reading_1,
    ?float $reading_2 = null,
    ?float $reading_3 = null,
    ?float $reading_4 = null,
    ?float $reading_5 = null,
    ?float $reading_6 = null,
    ?float $reading_7 = null,
    ?float $reading_8 = null,
    ?float $reading_9 = null,
    ?float $reading_10 = null,
    ?float $reading_11 = null,
    ?float $reading_12 = null,
    ?float $reading_13 = null,
    ?float $reading_14 = null,
    ?float $reading_15 = null,
    ?float $reading_16 = null,
    ?float $reading_17 = null,
    ?float $reading_18 = null,
    ?float $reading_19 = null,
    ?float $reading_20 = null,
    ?float $reading_21 = null,
    ?float $reading_22 = null,
    ?float $reading_23 = null,
    ?float $reading_24 = null,
    ?float $reading_25 = null,
    ?float $reading_26 = null,
    ?float $reading_27 = null,
    ?float $reading_28 = null,
    ?float $reading_29 = null,
    ?float $reading_30 = null,
    ?int $location_id = null,
    ?int $id = null,
    ?Location $location = null

    )
    {
        $this->obraz_plane = $obraz_plane;
        $this->average_illuminance = $average_illuminance;
        $this->illumination_uniformity = $illumination_uniformity;
        $this->normative_illuminance = $normative_illuminance;
        $this->normative_illumination_uniformity = $normative_illumination_uniformity;
        $this->reading_1 = $reading_1;
        $this->reading_2 = $reading_2;
        $this->reading_3 = $reading_3;
        $this->reading_4 = $reading_4;
        $this->reading_5 = $reading_5;
        $this->reading_6 = $reading_6;
        $this->reading_7 = $reading_7;
        $this->reading_8 = $reading_8;
        $this->reading_9 = $reading_9;
        $this->reading_10 = $reading_10;
        $this->reading_11 = $reading_11;
        $this->reading_12 = $reading_12;
        $this->reading_13 = $reading_13;
        $this->reading_14 = $reading_14;
        $this->reading_15 = $reading_15;
        $this->reading_16 = $reading_16;
        $this->reading_17 = $reading_17;
        $this->reading_18 = $reading_18;
        $this->reading_19 = $reading_19;
        $this->reading_20 = $reading_20;
        $this->reading_21 = $reading_21;
        $this->reading_22 = $reading_22;
        $this->reading_23 = $reading_23;
        $this->reading_24 = $reading_24;
        $this->reading_25 = $reading_25;
        $this->reading_26 = $reading_26;
        $this->reading_27 = $reading_27;
        $this->reading_28 = $reading_28;
        $this->reading_29 = $reading_29;
        $this->reading_30 = $reading_30;
        $this->location_id = $location_id;

        $this->id = $id;

        $this->location = $location;
    }

    public function __toString(): string
    {
        $ti = $this->location?->description_location ?? '?';
        return "{$ti}: {$this->obraz_plane}";
    }
}
