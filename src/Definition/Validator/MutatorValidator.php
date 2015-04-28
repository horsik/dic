<?php

namespace Kampaw\Dic\Definition\Validator;

use Kampaw\Dic\Definition\Mutator;

class MutatorValidator
{
    /**
     * @var string Error messages
     */
    const ARGUMENT_NOT_AN_ARRAY = 'Invalid mutator, argument is not an array';
    const NAME_KEY_MISSING      = 'Mutator name is missing';
    const NAME_INVALID_TYPE     = 'Invalid name, expected string';
    const NAME_EMPTY_STRING     = 'Invalid name, string cannot be empty';
    const NAME_INVALID_VALUE    = 'Invalid name, used invalid characters';
    const TYPE_INVALID_TYPE     = 'Invalid type, expected string';
    const TYPE_CLASS_NOT_FOUND  = 'Invalid type, class/interface not exists or cannot be autoloaded';
    const REF_INVALID_TYPE      = 'Invalid ref, expected string';
    const REF_INVALID_VALUE     = 'Invalid ref, used invalid characters';

    /**
     * @var string[] $errors
     */
    protected $errors = array();

    /**
     * @param array $mutator
     * @return bool
     */
    public function isValid($mutator)
    {
        $this->errors = array();

        if (!is_array($mutator)) {
            $this->errors[] = self::ARGUMENT_NOT_AN_ARRAY;
        }
        else {
            $this->checkName($mutator);
            $this->checkType($mutator);
            $this->checkRef($mutator);
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
     * @param array $mutator
     */
    protected function checkName(array $mutator)
    {
        if (!array_key_exists('name', $mutator)) {
            $this->errors[] = self::NAME_KEY_MISSING;
        }
        elseif (!is_string($mutator['name'])) {
            $this->errors[] = self::NAME_INVALID_TYPE;
        }
        elseif (empty($mutator['name'])) {
            $this->errors[] = self::NAME_EMPTY_STRING;
        }
        elseif (!preg_match('/\A[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\Z/', $mutator['name'])) {
            $this->errors[] = self::NAME_INVALID_VALUE;
        }
    }

    /**
     * @param array $mutator
     */
    protected function checkType(array $mutator)
    {
        if (!array_key_exists('type', $mutator)) {
            /* No type hint supplied, nothing to validate */
            return;
        }
        elseif (!is_string($mutator['type'])) {
            $this->errors[] = self::TYPE_INVALID_TYPE;
        }
        elseif (!class_exists($mutator['type']) and !interface_exists($mutator['type'])) {
            $this->errors[] = self::TYPE_CLASS_NOT_FOUND;
        }
    }

    /**
     * @param array $mutator
     */
    protected function checkRef(array $mutator)
    {
        if (!array_key_exists('ref', $mutator)) {
            /* No reference supplied, nothing to validate */
            return;
        }
        elseif (!is_string($mutator['ref'])) {
            $this->errors[] = self::REF_INVALID_TYPE;
        }
        elseif (!preg_match('/\A[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\Z/', $mutator['ref'])) {
            $this->errors[] = self::REF_INVALID_VALUE;
        }
    }
} 