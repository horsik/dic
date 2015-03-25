<?php

namespace Kampaw\Dic\Definition\ClassDefinition;

use Kampaw\Dic\Definition\Parameter\AbstractParameter;

class UnsanitizedClassDefinition extends AbstractClassDefinition
{
    /**
     * @param string $class
     */
    protected function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @param AbstractParameter[] $parameters
     */
    protected function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param Mutator[] $mutators
     */
    protected function setMutators(array $mutators)
    {
        $this->mutators = $mutators;
    }
}