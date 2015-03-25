<?php

namespace Kampaw\Dic\Definition\ClassDefinition;

use Kampaw\Dic\Definition\Parameter\UnsanitizedParameter;
use Kampaw\Dic\Exception\BadMethodCallException;
use Kampaw\Dic\Exception\DomainException;
use Kampaw\Dic\Exception\InvalidArgumentException;
use Kampaw\Dic\Definition\Parameter\SanitizedParameter;
use Kampaw\Dic\Definition\Parameter\AbstractParameter;
use Kampaw\Dic\Exception\ExceptionInterface;

class SanitizedClassDefinition extends AbstractClassDefinition
{
    /**
     * @return SanitizedParameter[]
     */
    public function getParameters()
    {
        return array_values($this->parameters);
    }

    /**
     * @param string $class
     */
    protected function setClass($class)
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
     * @param AbstractParameter[] $parameters
     */
    protected function setParameters(array $parameters)
    {
        foreach ($parameters as $parameter) {
            $this->parameters[$parameter->getName()] = $this->sanitizeParameter($parameter);
        }
    }

    /**
     * @param Mutator[] $mutators
     */
    protected function setMutators(array $mutators)
    {
        // TODO: Implement setMutators() method.
    }

    /**
     * @param AbstractParameter$parameter
     * @return SanitizedParameter
     */
    protected function sanitizeParameter(AbstractParameter $parameter)
    {
        $name = $parameter->getName();

        if (array_key_exists($name, $this->parameters)) {
            throw new BadMethodCallException("Parameter $$name already exists");
        }

        return new SanitizedParameter($name, $parameter->getType(), $parameter->getValue());
    }
}