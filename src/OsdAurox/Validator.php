<?php


namespace OsdAurox;


use DateTime;
use src\Field;

class Validator
{
    public string $field = ''; // Nom du champs
    public array $rule = []; // Règle en cours de tests
    public array $errors = []; // Erreur en train d'être levée

    public array $rules = []; // history des règles testées sur la session
    public array $fieldChecked = []; // liste des champ testés
    public array $fieldIgnored = []; // lite des champs ignorés


    public bool $valid = false;


    public function __construct()
    {
        $this->rules = [];

    }

    public function validate(array $rules, array $datasInput): array
    {
        $errors = [];

        $this->rules[] = $rules;
        $this->error = [];
        $this->fieldIgnored = array_diff_key(array_keys($datasInput), array_keys($rules));

        foreach ($rules as $fieldName => $fieldArray) {

            $input = $datasInput[$fieldName] ?? null;
            $field = new Field($fieldName, $fieldArray, $input);

            $this->fieldChecked[] = $fieldName;

            // si il y a des options, la valeur doit y être
            if ($field->options && !is_array($field->options)) {
                $error = I18n::t('value must be an options array');;
                $field->errors[] = $error;
                $this->errors[$fieldName] = $field->errors;
            }

            if ($field->type === 'integer') {
                $r = $this->validateIntType($field->input);
                if (!$r['valid']) {
                    $field->errors[] = $r['msg'];
                    $this->errors[$fieldName] = $field->errors;
                }
            }

            $this->notEditable = $rule['notEditable'] ?? false;
            $this->comment = $rule['comment'] ?? '';

        }


        foreach ($this->rules as $rule) {
            $resultRule = $rule($input);
            if ($resultRule['valid'] === false) {

                if ($this->optional && empty($input)) {
                    continue;
                } else {
                    $errors[] = [
                        'field' => $this->field,
                        'valid' => $resultRule['valid'],
                        'msg' => $resultRule['msg']
                    ];
                }
            }
        }
        return $errors;
    }


    public function isValid($input)
    {
        foreach ($this->rules as $rule) {
            $resultRule = $rule($input);
            if ($resultRule['valid'] === false) {
                return false;
            }
        }
        return true;
    }


    public function validateEmail(Field $field): array
    {
        if (!is_string($field->input)) {
            $valid = false;
        }

        if ($valid) {
            $valid = filter_var($field->input, FILTER_VALIDATE_EMAIL);
        }

        return ['valid' => $valid,
            'msg' => $msg ?? ''];

    }


    public function validateNotEmpty(Field $field): array
    {
        $msg = I18n::t('must not be empty');

        if (is_string($field->input)) {
            $field->input = trim($field->input);
        }

        $valid = !empty($field->input);

        return ['valid' => $valid,
            'msg' => $msg ?? ''];
    }

    public function validateRequired(Field $field): array
    {
        $msg = I18n::t('field is required');

        // Vérifie si la valeur est null ou undefined
        if ($field->input === null) {
            return ['valid' => false, 'msg' => $msg];
        }

        // Pour les chaînes de caractères
        if (is_string($field->input)) {
            return [
                'valid' => trim($field->input) !== '',
                'msg' => $msg
            ];
        }

        // Pour les tableaux
        if (is_array($field->input)) {
            return [
                'valid' => count($field->input) > 0,
                'msg' => $msg
            ];
        }

        // Pour les nombres
        if (is_numeric($field->input)) {
            return [
                'valid' => true,
                'msg' => $msg
            ];
        }

        // Pour les booléens
        if (is_bool($field->input)) {
            return [
                'valid' => true,
                'msg' => $msg
            ];
        }

        // Pour tous les autres types
        return [
            'valid' => !empty($field->input),
            'msg' => $msg
        ];

    }


    public function ValidateLength(Field $field, ?int $min = null, ?int $max = null): array
    {
        $min = $field->min ?? $min;
        $max = $field->max ?? $max;

        $msg = '';
        $valid = false;
        $inputLength = null;
        $minPass = false;
        $maxPass = false;

        // recherche de la taille
        if (is_string($field->input)) {
            $inputLength = (int)mb_strlen($field->input);
        }

        if (is_array($field->input)) {
            $inputLength = count($field->input);
        }

        if (is_object($field->input)) {
            $inputLength = count(get_object_vars($field->input));
        }

        if (is_int($field->input)) {
            $inputLength = mb_strlen((string)$field->input);
        }

        // validation
        if ($inputLength !== null) {

            // verification du min
            if ($min === null) {
                $minPass = true;
            } else {
                $minPass = $inputLength >= $min;
            }

            // verification du max
            if ($max === null) {
                $maxPass = true;
            } else {
                $maxPass = $inputLength <= $max;
            }

            $valid = $minPass && $maxPass;
        }

        if (!$valid) {
            if ($min && $max) {
                $msg = I18n::t('must be between {min} and {max} characters', ['min' => $min, 'max' => $max]);
            } elseif ($min) {
                $msg = I18n::t('must be at least {min} characters', ['min' => $min]);
            } else {
                $msg = I18n::t('must be at most {max} characters', ['max' => $max]);
            }
        }
        return ['valid' => $valid,
            'msg' => $msg ?? ''];

    }

    public function validateStringType(Field $field): array
    {
        $msg = I18n::t('must be a string');

        // Vérifie si la valeur est de type string
        $valid = is_string($field->input);

        return [
            'valid' => $valid,
            'msg' => $msg
        ];
    }

    public function validateIntType(Field $field): array
    {
        $msg = I18n::t('must be a int');

        // Vérifie si la valeur est de type string
        $valid = is_int($field->input);

        return [
            'valid' => $valid,
            'msg' => $msg
        ];

    }

    public function validateFloatType(Field $field): array
    {
        $msg = I18n::t('must be a float');

        // Vérifie si la valeur est de type string
        $valid = is_float($field->input);

        return [
            'valid' => $valid,
            'msg' => $msg
        ];

    }

    public function validateMin(Field $field): array
    {
        $minimum = $field->min ?? null;
        if(!$minimum){
            return [
                'valid' => true,
                'msg' => 'no min set'
            ];
        }
        $msg = I18n::t('must be greater than or equal to %s', [$minimum]);

        // Convertir les chaînes numériques en nombres
        if (is_string($field->input) && is_numeric($field->input)) {
            if (str_contains($field->input, '.')) {
                $field->input = (float)$field->input;
            } else {
                $field->input = (int)$field->input;
            }
        }

        // Vérifier si c'est un nombre
        if (!is_numeric($field->input)) {
            return [
                'valid' => false,
                'msg' => I18n::t('must be a number')
            ];
        }

        return [
            'valid' => $field->input >= $minimum,
            'msg' => $msg
        ];

    }


    public function validateMax(Field $field): array
    {
        $maximum = $field->max ?? null;
        if(!$maximum){
            return [
                'valid' => true,
                'msg' => 'no max set'
            ];
        }

        $msg = I18n::t('must be less than or equal to %s', [$maximum]);

        // Convertir les chaînes numériques en nombres
        if (is_string($field->input) && is_numeric($field->input)) {
            if (str_contains($field->input, '.')) {
                $field->input = (float)$field->input;
            } else {
                $field->input = (int)$field->input;
            }
        }

        // Vérifier si c'est un nombre
        if (!is_numeric($field->input)) {
            return [
                'valid' => false,
                'msg' => I18n::t('must be a number')
            ];
        }

        return [
            'valid' => $field->input <= $maximum,
            'msg' => $msg
        ];

    }


    public function validateStartWith(Field $field): array
    {
        if(!$field->startWithPrefix){
            return [
                'valid' => true,
                'msg' => 'no startWithPrefix set'
            ];
        }

        $prefix = $field->startWithPrefix;
        if(!$prefix){
           throw new \Exception('startWithPrefix is empty on Field');
        }
        $msg = I18n::t('must start with "%s"', [$prefix]);

        $caseSensitive = $field->startWithCaseSensitive ?? false;

        if (!is_string($field->input)) {
            return [
                'valid' => false,
                'msg' => I18n::t('must be a string')
            ];
        }

        if ($caseSensitive) {
            $valid = str_starts_with($field->input, $prefix);
        } else {
            $valid = str_starts_with(strtolower($field->input), strtolower($prefix));
        }

        return [
            'valid' => $valid,
            'msg' => $msg
        ];

    }

    public function validatePositive(Field $field): array
    {
        $msg = '';

        // Convertir les chaînes numériques en nombres
        if (is_string($field->input) && is_numeric($field->input)) {
            if (str_contains($field->input, '.')) {
                $field->input = (float)$field->input;
            } else {
                $field->input = (int)$field->input;
            }
        }

        // Vérifier si c'est un nombre
        if (!is_numeric($field->input)) {
            return [
                'valid' => false,
                'msg' => I18n::t('must be a positive number')
            ];
        }

        return [
            'valid' => $field->input > 0,
            'msg' => I18n::t('must be a positive number')
        ];

    }

    public function validateDate(Field $field): array
    {
        $format = $field->dateFormat;
        $msg = '';

        if (!is_string($field->input)) {
            return [
                'valid' => false,
                'msg' => I18n::t('must be a string date')
            ];
        }

        // Essayer de créer un objet DateTime
        $dateTime = DateTime::createFromFormat($format, $field->input);

        // Vérifier si la date est valide et si les erreurs de parsing sont présentes
        if (!$dateTime) {
            $valid = false;
        } else {
            $valid = true;
        }
        // Vérifier si la date correspond exactement au format attendu
        if ($valid) {
            $valid = $dateTime->format($format) === $field->input;
        }

        return [
            'valid' => $valid,
            'msg' => I18n::t('must be a valid date in format: %s', [$format])
        ];

    }

    public function validateDateTime(Field $field): array
    {
        $format = $field->datetimeFormat;
        $msg = '';

        if (!is_string($field->input)) {
            return [
                'valid' => false,
                'msg' => $msg
            ];
        }

        // Essayer de créer un objet DateTime
        $dateTime = DateTime::createFromFormat($format, $field->input);

        // Vérifier si la date est valide et si les erreurs de parsing sont présentes
        if (!$dateTime) {
            $valid = false;
        } else {
            $valid = true;
        }

        // Vérifier si la date correspond exactement au format attendu
        if ($valid) {
            $valid = $dateTime->format($format) === $field->input;
        }

        return [
            'valid' => $valid,
            'msg' => I18n::t('must be a valid datetime in format: %s', [$format])
        ];

    }


    /**
     * Vérifie si la valeur est présente dans un tableau donné
     *
     * @param array $allowedValues Tableau des valeurs autorisées
     * @param bool $strict Utiliser une comparaison stricte (===)
     * @return Validator
     */
    public function validateInArray(Field $field): array
    {
        $valid = in_array($field->input, $field->inArrayValues);

        // Crée un message lisible avec les valeurs autorisées
        $valuesString = implode(', ', array_map(function ($value) {
            if (is_null($value)) return 'null';
            if (is_bool($value)) return $value ? 'true' : 'false';
            return (string)$value;
        }, $field->inArrayValues));

        return [
            'valid' => $valid,
            'msg' => I18n::t("must be one of the following values : $valuesString")
        ];

    }


}