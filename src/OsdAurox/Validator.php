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

    public function validate($input)
    {
        $errors = [];
        foreach ($this->rules as $rule) {
            $resultRule = $rule($input);
            if($resultRule['valid'] === false) {
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
}