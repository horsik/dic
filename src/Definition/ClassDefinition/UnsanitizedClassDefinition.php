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
     * @param $parameter
     */
    protected function addParameter($parameter)
    {
        $this->parameters[] = $parameter;
    }

    /**
     * @param Mutator[] $mutators
     */
    protected function setMutators(array $mutators)
    {
        $this->mutators = $mutators;
    }
}