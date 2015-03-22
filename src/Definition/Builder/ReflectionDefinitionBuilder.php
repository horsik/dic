<?php

namespace Kampaw\Dic\Definition\Builder;

use Kampaw\Dic\Definition\ClassDefinition;
use Kampaw\Dic\Definition\Parameter;
use Kampaw\Dic\Exception\DomainException;

class ReflectionDefinitionBuilder
{
    /**
     * @param $name
     * @return ClassDefinition
     */
    public function getClassDefinition($name)
    {
        if (!class_exists($name)) {
            throw new DomainException("Class $name not exists and cannot be autoloaded");
        }

        $reflection = new \ReflectionClass($name);
        $parameters = array();

        if ($constructor = $reflection->getConstructor()) {
            foreach ($constructor->getParameters() as $parameter) {
                $parameters[] = new Parameter(
                    $this->getName($parameter),
                    $this->getClass($parameter),
                    $this->getValue($parameter)
                );
            }
        }

        return new ClassDefinition('\\' . $reflection->getName(), $parameters);
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return string
     */
    protected function getName(\ReflectionParameter $parameter)
    {
        return $parameter->getName();
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return string|null
     */
    protected function getClass(\ReflectionParameter $parameter)
    {
        if ($class = $parameter->getClass()) {
            return $class->getName();
        }
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return mixed
     */
    protected function getValue(\ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
    }
}