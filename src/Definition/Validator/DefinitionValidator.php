<?php

namespace Kampaw\Dic\Definition\Validator;

class DefinitionValidator
{
    /*
     * @var string Error messages
     */
    const ARGUMENT_NOT_AN_ARRAY        = 'Invalid definition, argument is not an array';
    const CONCRETE_KEY_MISSING         = 'Concrete class is missing';
    const CONCRETE_INVALID_TYPE        = 'Invalid concrete type, expected string';
    const CONCRETE_CLASS_NOT_FOUND     = 'Invalid concrete type, class not exists';
    const ABSTRACT_INVALID_TYPE        = 'Invalid abstract type, expected string';
    const ABSTRACT_INTERFACE_NOT_FOUND = 'Invalid abstract type, interface not exists';
    const ABSTRACT_BASECLASS_MISMATCH  = 'Invalid abstract type, expected instance of previously provided concrete type';
    const NAME_INVALID_TYPE            = 'Invalid name, expected string';
    const NAME_INVALID_VALUE           = 'Invalid name, used invalid characters';
    const LIFETIME_INVALID_TYPE        = 'Invalid lifetime, expected string';
    const LIFETIME_INVALID_VALUE       = 'Invalid lifetime, expected "transient" or "singleton"';
    const AUTOWIRE_INVALID_TYPE        = 'Invalid autowire mode, expected string';
    const AUTOWIRE_INVALID_VALUE       = 'Invalid autowire mode, expected "auto", "name" or "type"';
    const CANDIDATE_INVALID_TYPE       = 'Invalid candidate, expected boolean';

    /**
     * @var array $definitionErrors
     */
    protected $definitionErrors = array();

    /**
     * @var array $parametersErrors
     */
    protected $parametersErrors = array();

    /**
     * @var array $mutatorsErrors
     */
    protected $mutatorsErrors = array();

    /**
     * @var ParameterValidator $parameterValidator
     */
    protected $parameterValidator;

    /**
     * @var MutatorValidator $mutatorValidator
     */
    protected $mutatorValidator;

    public function __construct()
    {
        $this->parameterValidator = new ParameterValidator();
        $this->mutatorValidator = new MutatorValidator();
    }

    /**
     * @param array $definition
     * @return bool
     */
    public function isValid($definition)
    {
        $this->definitionErrors = array();

        if (!is_array($definition)) {
            $this->definitionErrors[] = self::ARGUMENT_NOT_AN_ARRAY;
        }
        else {
            $this->checkConcrete($definition);
//            $this->checkParameters($definition);
//            $this->checkMutators($definition);
            $this->checkAbstract($definition);
            $this->checkName($definition);
            $this->checkLifetime($definition);
            $this->checkAutowire($definition);
            $this->checkCandidate($definition);
        }

        return empty($this->definitionErrors);
    }

    /**
     * @return array
     */
    public function getDefinitionErrors()
    {
        return $this->definitionErrors;
    }

    /**
     * @param array $definition
     */
    protected function checkConcrete(array $definition)
    {
        if (!array_key_exists('concrete', $definition)) {
            $this->definitionErrors[] = self::CONCRETE_KEY_MISSING;
        }
        elseif (!is_string($definition['concrete'])) {
            $this->definitionErrors[] = self::CONCRETE_INVALID_TYPE;
        }
        elseif (!class_exists($definition['concrete'])) {
            $this->definitionErrors[] = self::CONCRETE_CLASS_NOT_FOUND;
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
            $this->definitionErrors[] = self::ABSTRACT_INVALID_TYPE;
        }
        elseif (!interface_exists($definition['abstract'])) {
            $this->definitionErrors[] = self::ABSTRACT_INTERFACE_NOT_FOUND;
        }
        elseif (!is_subclass_of($definition['concrete'], $definition['abstract'])) {
            $this->definitionErrors[] = self::ABSTRACT_BASECLASS_MISMATCH;
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
            $this->definitionErrors[] = self::NAME_INVALID_TYPE;
        }
        elseif (!preg_match('/\A[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\Z/', $definition['name'])) {
            $this->definitionErrors[] = self::NAME_INVALID_VALUE;
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
            $this->definitionErrors[] = self::LIFETIME_INVALID_TYPE;
        }
        elseif (!in_array($definition['lifetime'], array('transient', 'singleton'))) {
            $this->definitionErrors[] = self::LIFETIME_INVALID_VALUE;
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
            $this->definitionErrors[] = self::AUTOWIRE_INVALID_TYPE;
        }
        elseif (!in_array($definition['autowire'], array('none', 'auto', 'name', 'type'))) {
            $this->definitionErrors[] = self::AUTOWIRE_INVALID_VALUE;
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
        if (!is_bool($definition['candidate'])) {
            $this->definitionErrors[] = self::CANDIDATE_INVALID_TYPE;
        }
    }

    /**
     * @param array $definition
     */
    protected function checkParameters(array $definition)
    {
        // @todo(kampaw) to be implemented
    }

    /**
     * @param array $definition
     */
    protected function checkMutators(array $definition)
    {
        // @todo(kampaw) to be implemented
    }
}