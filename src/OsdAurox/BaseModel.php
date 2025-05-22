<?php

namespace OsdAurox;

use Exception;
use InvalidArgumentException;
use PDO;
use PDOException;
use RuntimeException;

class BaseModel {


    public const TABLE = "unknown";

    public int $id;

    public function getTable(): string
    {
        return static::TABLE;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }


    /**
     *
     * Alias pour récupérer un array via FETCH_ASSOC
     * Sqli possible via $select, $id est sécurisé
     *
     * @param $pdo
     * @param $id int safe
     * @param $select string  attention sqli possible
     * @return mixed
     */
    public static function get($pdo, $id, $select = '*')
    {
        try {
            $table = static::TABLE;
            $stmt = $pdo->prepare("SELECT $select FROM $table WHERE id = :id");
            $id = (int) $id;
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            throw new RuntimeException('Database connection error.');
        }
    }

    /**
     * Vérifie si un enregistrement avec l'ID spécifié existe dans la table
     *
     * @param PDO $pdo
     * @param int $id sécurisé
     * @return bool true si l'enregistrement existe, false sinon
     * @throws RuntimeException Si une erreur de connexion à la base de données survient
     */
    public static function exist($pdo, $id): bool
    {
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT)) {
                return false;
            }
            $id = (int) $id;
            if(empty($id)) {
                return false;
            }

            $table = static::TABLE;
            $stmt = $pdo->prepare("SELECT id FROM $table WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            throw new RuntimeException('Database connection error.');
        }
    }

    /**
     *
     * Permet de retourner un array via FETCH_ASSOC en cherchant par un champ spécifique
     *
     * Sqli possible sur le champ $field
     *
     * @param string $field attention sqli possible, le nom de la colonne où chercher
     * @param mixed $value  sécurisé, la valeur qu'on cherche
     *
     * @return array|false L'enregistrement récupéré sous forme de tableau associatif, ou false si aucun enregistrement n'est trouvé.
     * * @throws RuntimeException Si une erreur de connexion à la base de données survient.
     */
    public static function getBy($pdo, string $field, mixed $value): array|false
    {
        try {
            $table = static::TABLE;
            $stmt = $pdo->prepare("SELECT * FROM $table WHERE $field = :search");
            $stmt->execute(['search' => $value]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            throw new RuntimeException('Database connection error.');
        }
    }

    /**
     *
     * Permet de retourner un array via FETCH_ASSOC en cherchant par un champ spécifique
     *
     * Sqli possible sur le champ $field
     *
     * @param string $field attention sqli possible, le nom de la colonne où chercher
     * @param mixed $value  sécurisé, la valeur qu'on cherche
     *
     * @return array L'enregistrement récupéré sous forme de tableau associatif, ou false si aucun enregistrement n'est trouvé.
     * @throws RuntimeException Si une erreur de connexion à la base de données survient.
     */
    public static function getAllBy($pdo, string $field, mixed $value): array
    {
        try {
            $table = static::TABLE;
            $stmt = $pdo->prepare("SELECT * FROM $table WHERE $field = :search");
            $stmt->execute(['search' => $value]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            throw new RuntimeException('Database connection error.');
        }
    }

    public static function count($pdo)
    {
        $table = static::TABLE;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Supprime une entrée en BDD
     *
     * @param $pdo
     * @param int $id  sécurisé
     * @return bool
     */
    public static function delete($pdo, int $id): bool
    {
        $id = (int) $id;
        $stmt = $pdo->prepare("DELETE FROM " . static::TABLE . " WHERE id = :id");
        $stmt->execute(['id' => $id]);
        if($stmt->rowCount() == 0) {
            return false;
        }
        return true;
    }

    /**
     * Regarde si la valeur pour le champ est unique en table, sqli possible sur $field, securisé sur $value
     * Not secure sql injection possible via $field
     * @param $pdo
     * @param string $field  sqli possible champ à verifier
     * @param mixed $value  sécurisé, motif à chercher
     * @return bool
     */
    public static function check_uniq($pdo, string $field, mixed $value): bool
    {
        $table = static::TABLE;
        $stmt = $pdo->prepare("SELECT $field FROM $table WHERE $field = :value");
        $stmt->execute(['value' => $value]);
        $entity = $stmt->fetch();
        if ($entity) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * Retourne les règles de validation de type OsdAurox\Validator
     *
     * @return array
     * @throws Exception
     */
    public static function getRules(): array
    {
        # Return respect/validation rules
        throw new Exception('Not implemented');
    }

    public static function validate(): bool
    {
        throw new Exception('Not implemented');
    }

    /**
     *  Raccourcis pour extraire un JSON_ARRAYAGG ou [ ] si erreur; d'un résultat Array PDO
     *
     * @param array $array
     * @param string $key
     * @param array|null $default
     * @return array
     */
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

    /**
     *
     * Alias pour récupérer des entrées SQL FETCH_ASSOC par une list d'Ids
     *
     * Attention Sqli - Injection SQL possible sur $table et sur $select, doit être sécurisé et
     * ne pas venir d'une saisie utilisateur
     *
     * $ids est sécurisé par PDO + cast peut provenir d'un formulaire
     *
     * @param $pdo
     * @param string $table
     * @param array $ids
     * @return array
     */
    public static function getByIds($pdo, string $table, array $ids, string $select='*'): array
    {
        if (empty($ids)) {
            return [];
        }

        // on met à plat le tableau
        $ids = array_values($ids);

        $placeholders = [];
        for ($i = 0; $i < count($ids); $i++) {
            $placeholders[] = ':id' . $i;
        }
        $sql = "SELECT $select FROM $table WHERE id IN (" . implode(',', $placeholders) . ")";
        $stmt = $pdo->prepare($sql);

        for ($i = 0; $i < count($ids); $i++) {
            $val = $ids[$i];
            if (!filter_var($val, FILTER_VALIDATE_INT)) {
                throw new InvalidArgumentException('ID invalide, il faut un entier');
            }
            $val = (int)$val;
            $stmt->bindValue(':id' . $i, $val, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function idsExistsOrEmpty($pdo, string $table, array $ids): bool
    {
        if (empty($ids)) {
            return True;
        }

        // on met à plat le tableau
        $ids = array_values($ids);

        $placeholders = [];
        for ($i = 0; $i < count($ids); $i++) {
            $placeholders[] = ':id' . $i;
        }
        $sql = "SELECT COUNT(id) as count FROM $table WHERE id IN (" . implode(',', $placeholders) . ")";
        $stmt = $pdo->prepare($sql);

        for ($i = 0; $i < count($ids); $i++) {
            $val = $ids[$i];
            if (!filter_var($val, FILTER_VALIDATE_INT)) {
                throw new InvalidArgumentException('ID invalide, il faut un entier');
            }
            $val = (int)$val;
            $stmt->bindValue(':id' . $i, $val, PDO::PARAM_INT);
        }

        $stmt->execute();
        $r = $stmt->fetch();
        return $r['count'] > 0;
    }
}