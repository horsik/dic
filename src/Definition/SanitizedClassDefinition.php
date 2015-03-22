<?php

namespace Kampaw\Dic\Definition;

use Kampaw\Dic\Exception\DomainException;
use Kampaw\Dic\Exception\InvalidArgumentException;

class SanitizedClassDefinition extends ClassDefinition
{
    /**
     * @var string $class
     */
    protected $class;

    /**
     * @var Parameter[] $parameters
     */
    protected $parameters = array();
    protected $mutators = array();

    /**
     * @param ClassDefinition $definition
     */
    public function __construct(ClassDefinition $definition)
    {
        $this->setClass($definition->getClass());

        if ($parameters = $definition->getParameters()) {
            $this->setParameters($parameters);
        }
    }

    /**
     * @return Parameter[]
     */
    public function getParameters()
    {
        return array_values($this->parameters);
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        if (!is_string($class)) {
            throw new InvalidArgumentException("Class name must be a string");
        }

        if (!class_exists($class)) {
            throw new DomainException("Class $class not exists and cannot be autoloaded");
        }

        if ($class[0] !== '\\') {
            $class = '\\' . $class;
        }

        $this->class = $class;
    }

    /**
     * @param Parameter[] $parameters
     */
    public function setParameters(array $parameters)
    {
        foreach ($parameters as $parameter) {
            if (!($parameter instanceof Parameter)) {
                throw new InvalidArgumentException('All parameters must be an instance of Parameter');
            }

            $name = $parameter->getName();

            if (array_key_exists($name, $this->parameters)) {
                throw new InvalidArgumentException("Parameter $$name already exists");
            }

            $this->parameters[$name] = $parameter;
        }
    }
}