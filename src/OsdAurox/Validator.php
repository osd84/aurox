<?php


namespace OsdAurox;


class Validator
{

    private array $rules = [];
    public string $field = '';

    // crée un nouveau Validateur
    public static function create($field): Validator
    {
        $validator = new Validator();
        $validator->field = Sec::hNoHtml($field);
        return $validator;
    }


    public function validate($input)
    {
        $errors = [];
        foreach ($this->rules as $rule) {
            $resultRule = $rule($input);
            if ($resultRule['valid'] === false) {
                $errors[] = [
                    'field' => $this->field,
                    'valid' => $resultRule['valid'],
                    'msg' => $resultRule['msg']
                ];
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


    public function email(): Validator
    {
        $this->rules[] = function ($input) {
            $msg = I18n::t('must be valid email');
            $valid = true;

            if (!is_string($input)) {
                $valid = false;
            }

            if ($valid) {
                $valid = filter_var($input, FILTER_VALIDATE_EMAIL);
            }

            return ['valid' => $valid,
                'msg' => $msg ?? ''];

        };

        return $this;
    }


    public function notEmpty(): Validator
    {
        $this->rules[] = function ($input) {
            $msg = I18n::t('must not be empty');

            if (!is_string($input)) {
                $input = trim($input);

            }

            $valid = !empty($input);

            return ['valid' => $valid,
                'msg' => $msg ?? ''];

        };

        return $this;

    }

    public function required(): Validator
    {
        $this->rules[] = function ($input) {
            $msg = I18n::t('field is required');

            // Vérifie si la valeur est null ou undefined
            if ($input === null) {
                return ['valid' => false, 'msg' => $msg];
            }

            // Pour les chaînes de caractères
            if (is_string($input)) {
                return [
                    'valid' => trim($input) !== '',
                    'msg' => $msg
                ];
            }

            // Pour les tableaux
            if (is_array($input)) {
                return [
                    'valid' => count($input) > 0,
                    'msg' => $msg
                ];
            }

            // Pour les nombres
            if (is_numeric($input)) {
                return [
                    'valid' => true,
                    'msg' => $msg
                ];
            }

            // Pour les booléens
            if (is_bool($input)) {
                return [
                    'valid' => true,
                    'msg' => $msg
                ];
            }

            // Pour tous les autres types
            return [
                'valid' => !empty($input),
                'msg' => $msg
            ];
        };

        return $this;
    }


    public function length(?int $min = null, ?int $max = null): Validator
    {
        $this->rules[] = function ($input) use ($min, $max) {

            $msg = '';
            $valid = false;
            $inputLength = null;
            $minPass = false;
            $maxPass = false;

            // recherche de la taille
            if (is_string($input)) {
                $inputLength = (int)mb_strlen($input);
            }

            if (is_array($input)) {
                $inputLength = count($input);
            }

            if (is_object($input)) {
                return count(get_object_vars($input));
            }

            if (is_int($input)) {
                return mb_strlen((string)$input);
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
                if($min && $max) {
                    $msg = I18n::t('must be between {min} and {max} characters', ['min' => $min, 'max' => $max]);
                } elseif ($min) {
                    $msg = I18n::t('must be at least {min} characters', ['min' => $min]);
                } else {
                    $msg = I18n::t('must be at most {max} characters', ['max' => $max]);
                }
            }
            return ['valid' => $valid,
                'msg' => $msg ?? ''];
        };

        return $this;
    }
}