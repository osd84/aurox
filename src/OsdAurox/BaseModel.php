<?php

namespace OsdAurox;

use Exception;
use PDOException;
use RuntimeException;

class BaseModel {


    public static string $table = "unknown";

    public int $id;

    public function getTable(): string
    {
        return static::$table;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }


    public static function get($pdo, $id)
    {
        try {
            $table = static::$table;
            $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = :id");
            $id = (int) $id;
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            throw new RuntimeException('Database connection error.');
        }
    }

    /**
     * Not secure sql injection possible via $field
     * Retrieves a single record from the database table based on the specified field and value.
     *
     * @param string $field The name of the column to search.
     * @param mixed $value The value to match against the specified column.
     *
     * @return array|false The fetched record as an associative array, or false if no record is found.
     * @throws RuntimeException If there is a database connection error.
     */
    public static function getBy($pdo, string $field, mixed $value): array|false
    {
        try {
            $table = static::$table;
            $f_token = ':' . $field;
            $stmt = $pdo->prepare("SELECT * FROM $table WHERE $field = $f_token");
            $stmt->execute([$field => $value]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            throw new RuntimeException('Database connection error.');
        }
    }

    /**
     * Not secure sql injection possible via $field
     * Retrieves all records from the database where the specified field matches the given value.
     *
     * @param string $field The name of the database column to filter by.
     * @param mixed $value The value to match against the specified column.
     * @return array An array of fetched records as associative arrays.
     * @throws RuntimeException If a database connection error occurs.
     */
    public static function getAllBy($pdo, string $field, mixed $value): array
    {
        try {
            $table = static::$table;
            $f_token = ':' . $field;
            $stmt = $pdo->prepare("SELECT * FROM $table WHERE $field = $f_token");
            $stmt->execute([$field => $value]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            throw new RuntimeException('Database connection error.');
        }
    }

    public static function count($pdo)
    {
        $table = static::$table;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function delete($pdo, $id): bool
    {
        $id = (int) $id;
        $stmt = $pdo->prepare("DELETE FROM " . static::$table . " WHERE id = :id");
        $stmt->execute(['id' => $id]);
        if($stmt->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
    * Not secure sql injection possible via $field
    */
    public static function check_uniq($pdo, $field, $value): bool
    {
        $table = static::$table;
        $stmt = $pdo->prepare("SELECT $field FROM $table WHERE $field = :value");
        $stmt->execute(['value' => $value]);
        $entity = $stmt->fetch();
        if ($entity) {
            return false;
        } else {
            return true;
        }
    }

    public static function getRules()
    {
        # Return respect/validation rules
        throw new Exception('Not implemented');
    }

    public static function validate()
    {
        throw new Exception('Not implemented');
    }

    public static function jsonArrayAggDecode(array $array, string $key, array $default = null): array
    {
        if(!is_array($default)) {
            $default = [];
        }

        if (!array_key_exists($key, $array)) {
            return $default;
        }

        $json = $array[$key];
        if (!$json) {
            return $default;
        }
        $decoded = json_decode($json, true);
        if ($decoded === null) {
            return $default;
        }
        return $decoded;
    }
}