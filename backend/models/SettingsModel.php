<?php

/**
 * SettingsModel — key/value store for the `settings` table.
 * Batavia Madrigal Singers — Backend API
 */

require_once __DIR__ . '/../config/database.php';

class SettingsModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /**
     * Return all settings as a key => value array.
     */
    public function all(): array
    {
        try {
            $rows = $this->db->query('SELECT `key`, `value` FROM settings')->fetchAll();
        } catch (PDOException $e) {
            // Table missing (migration 005 not run yet) → behave as empty
            if ($e->getCode() === '42S02') {
                return [];
            }
            throw $e;
        }

        $out = [];
        foreach ($rows as $row) {
            $out[$row['key']] = $row['value'];
        }
        return $out;
    }

    /**
     * Upsert multiple settings. Only whitelisted keys are stored.
     *
     * @param  array $settings  key => value pairs.
     * @return array            The full settings array after saving.
     */
    public function setMany(array $settings): array
    {
        $allowed = ['hero_background'];

        $stmt = $this->db->prepare(
            'INSERT INTO settings (`key`, `value`)
             VALUES (:key, :value)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)'
        );

        foreach ($settings as $key => $value) {
            if (!in_array($key, $allowed, true)) {
                continue;
            }
            $stmt->execute([
                ':key'   => $key,
                ':value' => $this->cleanValue($value),
            ]);
        }

        return $this->all();
    }

    private function cleanValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = is_string($value) ? trim($value) : (string) $value;
        if ($value === '') {
            return null;
        }
        return substr($value, 0, 65535);
    }
}
