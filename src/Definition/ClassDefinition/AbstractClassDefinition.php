<?php

namespace Kampaw\Dic\Definition\ClassDefinition;

use Kampaw\Dic\Definition\DefinitionInterface;
use Kampaw\Dic\Definition\Parameter\AbstractParameter;

abstract class AbstractClassDefinition implements DefinitionInterface
{
    /**
     * @var string $class
     */
    protected $class;

    /**
     * @var AbstractParameter[] $parameters
     */
    protected $parameters = array();

    /**
     * @var Mutator[] $mutators
     */
    protected $mutators = array();

    /**
     * @param string $class
     * @param AbstractParameter[] $parameters
     * @param array $mutators
     */
    public function __construct($class, array $parameters = array(), array $mutators = array())
    {
        $this->setClass($class);
        $this->setParameters($parameters);
        $this->setMutators($mutators);
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    protected abstract function setClass($class);

    /**
     * @return AbstractParameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    protected abstract function setParameters(array $parameters);

    /**
     * @return Mutator[]
     */
    public function getMutators()
    {
        return $this->mutators;
    }

    /**
     * @param Mutator[] $mutators
     */
    protected abstract function setMutators(array $mutators);
}