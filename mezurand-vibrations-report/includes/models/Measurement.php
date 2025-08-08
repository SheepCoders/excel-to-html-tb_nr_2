<?php

class Measurement
{
    public ?int $id = null;
    public float $ax;
    public float $ay;
    public float $az;
    public ?Activity $activity = null;


    public function save(int $activity_id): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'measurement';

        $data = [

            'activity_id' => $activity_id,
            'ax' => $this->ax,
            'ay' => $this->ay,
            'az' => $this->az,
        ];

        // Если есть ID - обновляем, иначе создаем новую запись
        if ($this->id !== null) {
            if (!$this->exists_in_db()) {
                throw new RuntimeException("Measurement with ID {$this->id} does not exist");
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
            $wpdb->prepare("SELECT 1 FROM {$wpdb->prefix}measurement WHERE id = %d", $this->id)
        );
    }


    public static function get_all(): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'measurement';

        $results = $wpdb->get_results("SELECT * FROM $table");

        $measurements = [];
        foreach ($results as $row) {
            $measurements[] = new Measurement(
                (float)$row->ax,
                (float)$row->ay,
                (float)$row->az,
                (int)$row->id
            );
        }
        return $measurements;
    }


    public static function get_by_id(int $id): ?Measurement
    {
        global $wpdb;
        $table = $wpdb->prefix . 'measurement';

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));

        if ($row) {
            $activity = null;
            if (!empty($row->activity_id)) {
                $activity = Activity::get_by_id((int)$row->activity_id);
            }

            return new Measurement(
                (float)$row->ax,
                (float)$row->ay,
                (float)$row->az,
                (int)$row->id,
                $activity
            );
        }

        return null;
    }


    public static function get_by_activity_id(int $activity_id): array {
        global $wpdb;
        $table = $wpdb->prefix . 'measurement';
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE activity_id = %d", $activity_id
        ));

        $measurements = [];
        foreach ($results as $row) {
            $measurements[] = new Measurement(
                $row->ax,
                $row->ay,
                $row->az,
                $row->id, // Передаем ID
                null      // Activity можно установить позже
            );
        }
        return $measurements;
    }


    public static function delete(int $id): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'measurement';
        return (bool) $wpdb->delete($table, ['id' => $id]);
    }


    public function __construct(float $ax, float $ay, float $az, ?int $id = null, ?Activity $activity = null)
    {
        if ($ax < 0 || $ay < 0 || $az < 0) {
            throw new InvalidArgumentException("Values for ax, ay, az must be >= 0");
        }
        $this->id = $id;
        $this->ax = $ax;
        $this->ay = $ay;
        $this->az = $az;
        $this->activity = $activity;
    }

    public function __toString(): string
    {
        $ti = $this->activity?->measurement_time_Ti ?? '?';
        return "{$ti}: {$this->ax} {$this->ay} {$this->az}";
    }
}
