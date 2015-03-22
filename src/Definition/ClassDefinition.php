<?php

namespace Kampaw\Dic\Definition;

/**
 * @codeCoverageIgnore
 */
class ClassDefinition implements DefinitionInterface
{
    /**
     * @var string $class
     */
    protected $class;

    /**
     * @var Parameter[] $parameters
     */
    protected $parameters = array();

    /**
     * @var Mutator[] $mutators
     */
    protected $mutators = array();

    /**
     * @param string $class
     * @param Parameter[] $parameters
     * @param array $mutators
     */
    public function __construct($class, $parameters = array(), $mutators = array())
    {
        $this->class = $class;
        $this->parameters = $parameters;
        $this->mutators = $mutators;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return Mutator[]
     */
    public function getMutators()
    {
        return $this->mutators;
    }
}