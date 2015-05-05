<?php

namespace Kampaw\Dic\Config\Validator;

class DefinitionArrayValidator
{
    /*
     * @var string Specific error messages
     */
    const ARGUMENT_NOT_AN_ARRAY   = 'Invalid definition, argument is not an array';
    const CONCRETE_KEY_MISSING    = 'Concrete class is missing';
    const CONCRETE_INVALID_TYPE   = 'Invalid concrete type, expected string';
    const CONCRETE_TYPE_NOT_FOUND = 'Invalid concrete type, class not exists';
    const ABSTRACT_INVALID_TYPE   = 'Invalid abstract type, expected string';
    const ABSTRACT_TYPE_NOT_FOUND = 'Invalid abstract type, interface not exists';
    const ABSTRACT_TYPE_MISMATCH  = 'Invalid abstract type, expected a parent of provided concrete type';
    const NAME_INVALID_TYPE       = 'Invalid name, expected string';
    const NAME_INVALID_VALUE      = 'Invalid name, used invalid characters';
    const PARAMETERS_INVALID_TYPE = 'Invalid parameters, expected array';
    const LIFETIME_INVALID_TYPE   = 'Invalid lifetime, expected string';
    const LIFETIME_INVALID_VALUE  = 'Invalid lifetime, expected "transient" or "singleton"';
    const AUTOWIRE_INVALID_TYPE   = 'Invalid autowire mode, expected string';
    const AUTOWIRE_INVALID_VALUE  = 'Invalid autowire mode, expected "none", "auto", "name" or "type"';
    const CANDIDATE_INVALID_TYPE  = 'Invalid candidate, expected boolean';
    const CIRCULAR_DEPENDENCY     = 'Invalid parameter type, circular dependency';

    /**
     * @var array $errors
     */
    protected $errors = array();

    /**
     * @var array $pErrors
     */
    protected $pErrors = array();

    /**
     * @var array $mErrors
     */
    protected $mErrors = array();

    /**
     * @var ParameterArrayValidator $pValidator
     */
    protected $pValidator;

    /**
     * @var MutatorArrayValidator $mValidator
     */
    protected $mValidator;

    public function __construct()
    {
        $this->pValidator = new ParameterArrayValidator();
        $this->mValidator = new MutatorArrayValidator();
    }

    /**
     * @param array $definition
     * @return bool
     */
    public function isValid($definition)
    {
        $this->errors = array();
        $this->pErrors = array();

        if (!is_array($definition)) {
            $this->errors[] = self::ARGUMENT_NOT_AN_ARRAY;
        }
        else {
            $this->checkConcrete($definition);
            $this->checkParameters($definition);
            $this->checkMutators($definition);
            $this->checkAbstract($definition);
            $this->checkName($definition);
            $this->checkLifetime($definition);
            $this->checkAutowire($definition);
            $this->checkCandidate($definition);
        }

        return empty($this->errors)
            && empty($this->pErrors);
    }

    /**
     * @return array
     */
    public function getDefinitionErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getParametersErrors()
    {
        return $this->pErrors;
    }

    /**
     * @param array $definition
     */
    protected function checkConcrete(array $definition)
    {
        if (!array_key_exists('concrete', $definition)) {
            $this->errors[] = self::CONCRETE_KEY_MISSING;
        }
        elseif (!is_string($definition['concrete'])) {
            $this->errors[] = self::CONCRETE_INVALID_TYPE;
        }
        elseif (!class_exists($definition['concrete'])) {
            $this->errors[] = self::CONCRETE_TYPE_NOT_FOUND;
        }
    }

    /**
     * @param array $definition
     */
    protected function checkAbstract(array $definition)
    {
        if (!array_key_exists('abstract', $definition)) {
            /* No abstract type supplied, nothing to validate */
            return;
        }
        elseif (!is_string($definition['abstract'])) {
            $this->errors[] = self::ABSTRACT_INVALID_TYPE;
        }
        elseif (!interface_exists($definition['abstract'])) {
            $this->errors[] = self::ABSTRACT_TYPE_NOT_FOUND;
        }
        elseif (!is_subclass_of($definition['concrete'], $definition['abstract'])) {
            $this->errors[] = self::ABSTRACT_TYPE_MISMATCH;
        }
    }

    /**
     * @param array $definition
     */
    protected function checkName(array $definition)
    {
        if (!array_key_exists('name', $definition)) {
            /* No name supplied, nothing to validate */
            return;
        }
        elseif (!is_string($definition['name'])) {
            $this->errors[] = self::NAME_INVALID_TYPE;
        }
        elseif (!preg_match('/\A[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\Z/', $definition['name'])) {
            $this->errors[] = self::NAME_INVALID_VALUE;
        }
    }

    /**
     * @param array $definition
     */
    protected function checkLifetime(array $definition)
    {
        if (!array_key_exists('lifetime', $definition)) {
            /* No lifetime supplied, nothing to validate */
            return;
        }
        elseif (!is_string($definition['lifetime'])) {
            $this->errors[] = self::LIFETIME_INVALID_TYPE;
        }
        elseif (!in_array($definition['lifetime'], array('transient', 'singleton'))) {
            $this->errors[] = self::LIFETIME_INVALID_VALUE;
        }
    }

    /**
     * @param array $definition
     */
    protected function checkAutowire(array $definition)
    {
        if (!array_key_exists('autowire', $definition)) {
            /* No autowire mode supplied, nothing to validate */
            return;
        }
        elseif (!is_string($definition['autowire'])) {
            $this->errors[] = self::AUTOWIRE_INVALID_TYPE;
        }
        elseif (!in_array($definition['autowire'], array('none', 'auto', 'name', 'type'))) {
            $this->errors[] = self::AUTOWIRE_INVALID_VALUE;
        }
    }

    /**
     * @param array $definition
     */
    protected function checkCandidate(array $definition)
    {
        if (!array_key_exists('candidate', $definition)) {
            /* No candidate supplied, nothing to validate */
            return;
        }
        elseif (!is_bool($definition['candidate'])) {
            $this->errors[] = self::CANDIDATE_INVALID_TYPE;
        }
    }

    /**
     * @param array $definition
     */
    protected function checkParameters(array $definition)
    {
        if (!array_key_exists('parameters', $definition)) {
            /* No parameters supplied, nothing to validate */
            return;
        }
        elseif (!is_array($definition['parameters'])) {
            $this->errors[] = self::PARAMETERS_INVALID_TYPE;
        }
        else {
            array_walk($definition['parameters'], array($this, 'checkParameter'), $definition);
        }
    }

    /**
     * @param array $parameter
     * @param int $key
     * @param array $definition
     */
    protected function checkParameter($parameter, $key, $definition)
    {
        if (!$this->pValidator->isValid($parameter)) {
            $this->pErrors[$key] = $this->pValidator->getErrors();
        }

        if (!array_key_exists('concrete', $definition)) {
            /* Definition malformed, bail out */
            return;
        }
        elseif (!array_key_exists('type', $parameter)) {
            /* No parameter type supplied, nothing to validate */
            return;
        }
        elseif ($parameter['type'] === $definition['concrete']) {
            $this->pErrors[$key][] = self::CIRCULAR_DEPENDENCY;
        }
    }

    /**
     * @param array $definition
     */
    protected function checkMutators(array $definition)
    {
        // @todo(kampaw) to be implemented
    }
}