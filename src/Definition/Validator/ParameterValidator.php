<?php

namespace Kampaw\Dic\Definition\Validator;

class ParameterValidator
{
    /**
     * @var string Error messages
     */
    const ARGUMENT_NOT_AN_ARRAY = 'Invalid parameter, argument is not an array';
    const NAME_KEY_MISSING      = 'Parameter name is missing';
    const NAME_INVALID_TYPE     = 'Invalid name, expected string';
    const NAME_EMPTY_STRING     = 'Invalid name, string cannot be empty';
    const NAME_INVALID_VALUE    = 'Invalid name, used reserved word or invalid characters';
    const TYPE_INVALID_TYPE     = 'Invalid type, expected string';
    const TYPE_CLASS_NOT_FOUND  = 'Invalid type, class/interface not exists or cannot be autoloaded';
    const REF_INVALID_TYPE      = 'Invalid ref, expected string';
    const VALUE_TYPE_MISMATCH   = 'Invalid value, expected instance of previously provided type';

    /**
     * @var string[] $errors
     */
    protected $errors = array();

    /**
     * @param array $parameter
     * @return bool
     */
    public function isValid($parameter)
    {
        $this->errors = array();

        if (!is_array($parameter)) {
            $this->errors[] = self::ARGUMENT_NOT_AN_ARRAY;
        } else {
            $this->checkName($parameter);
            $this->checkType($parameter);
            $this->checkValue($parameter);
            $this->checkRef($parameter);
        }

        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $parameter
     */
    protected function checkName(array $parameter)
    {
        if (!array_key_exists('name', $parameter)) {
            $this->errors[] = self::NAME_KEY_MISSING;
        }
        elseif (!is_string($parameter['name'])) {
            $this->errors[] = self::NAME_INVALID_TYPE;
        }
        elseif (empty($parameter['name'])) {
            $this->errors[] = self::NAME_EMPTY_STRING;
        }
        elseif ($parameter['name'] === 'this') {
            $this->errors[] = self::NAME_INVALID_VALUE;
        }
        elseif (!preg_match('/\A[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\Z/', $parameter['name'])) {
            $this->errors[] = self::NAME_INVALID_VALUE;
        }
    }

    /**
     * @param array $parameter
     */
    protected function checkType(array $parameter)
    {
        if (!array_key_exists('type', $parameter)) {
            /* No type hint supplied, nothing to validate */
            return;
        }
        elseif (!is_string($parameter['type'])) {
            $this->errors[] = self::TYPE_INVALID_TYPE;
        }
        elseif (!class_exists($parameter['type']) and !interface_exists($parameter['type'])) {
            $this->errors[] = self::TYPE_CLASS_NOT_FOUND;
        }
    }

    /**
     * @param array $parameter
     */
    protected function checkValue(array $parameter)
    {
        if (!array_key_exists('value', $parameter)) {
            /* No default value supplied, nothing to validate */
            return;
        }
        elseif (!array_key_exists('type', $parameter)) {
            /* No type hint supplied, accept all values */
            return;
        }
        elseif (!is_string($parameter['type'])) {
            /* Type hint malformed, bail out */
            return;
        }
        elseif (!is_a($parameter['value'], $parameter['type'])) {
            $this->errors[] = self::VALUE_TYPE_MISMATCH;
        }
    }

    /**
     * @param array $parameter
     */
    protected function checkRef(array $parameter)
    {
        if (!array_key_exists('ref', $parameter)) {
            /* No reference supplied, nothing to validate */
            return;
        }
        elseif (!is_string($parameter['ref'])) {
            $this->errors[] = self::REF_INVALID_TYPE;
        }
    }
}