<?php

class Location
{
    public ?int $id = null;

    // Основные поля
    public ?string $description_location = null;
    public ?string $description_light = null;
    public int $round_up_to = 1;

    public ?float $U = null;
    public ?string $uwagi = null;
    public ?string $luks_before = null;
    public ?string $luks_after = null;
    public ?string $legend = null;

    /** @var Obraz[] */
    public array $obrazy = [];

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
        $table = $wpdb->prefix . 'location';

        $data = [
            'description_location' => $this->description_location,
            'description_light' => $this->description_light,
            'round_up_to' => $this->round_up_to,
            'U' => $this->U,
            'uwagi' => $this->uwagi,
            'luks_before' => $this->luks_before,
            'luks_after' => $this->luks_after,
            'legend' => $this->legend,
        ];

        // Если есть id - обновляем, иначе вставляем новую запись
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
            $wpdb->prepare("SELECT 1 FROM {$wpdb->prefix}location WHERE id = %d", $this->id)
        );
    }


    public static function get_all(): array {
        global $wpdb;
        $table = $wpdb->prefix . 'location';

        // Сортировка по id DESC (от новых к старым)
        $results = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");

        $locations = [];
        foreach ($results as $row) {
            $location = new Location((array)$row);
            $location->obrazy = Obraz::get_by_location_id($row->id);
            $locations[] = $location;
        }
        return $locations;
    }


    public static function get_by_id(int $id): ?Location {
        global $wpdb;
        $table = $wpdb->prefix . 'location';

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
        if ($row) {
            $location = new self((array)$row);
            $location->obrazy = Obraz::get_by_location_id($row->id);
            return $location;
        }
        return null;
    }


    public static function get_by_obraz_id(int $obraz_id): ?Location
    {
        global $wpdb;
        $obraz_table = $wpdb->prefix . 'obraz';

        // Получаем location_id для данного obraz_id
        $location_id = $wpdb->get_var(
            $wpdb->prepare("SELECT location_id FROM $obraz_table WHERE id = %d", $obraz_id)
        );

        if ($location_id === null) {
            return null; // Измерение не найдено
        }

        // Возвращаем Location по найденному location_id
        return self::get_by_id((int)$location_id);
    }


    public static function delete(int $id): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'location';
        return (bool) $wpdb->delete($table, ['id' => $id]);
    }


    public function __toString(): string
    {
        return "{$this->description_location} / {$this->description_light}";
    }
}
