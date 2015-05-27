<?php

namespace Kampaw\Dic\Definition;

class ArrayDefinition extends AbstractDefinition
{
    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->compileParameters($this->parameters);
        $this->compileMutators($this->mutators);
    }

    /**
     * @param array $parameters
     */
    protected function compileParameters(array &$parameters)
    {
        foreach ($parameters as &$context) {
            $context = new Parameter($context);
        }
    }

    /**
     * @param array $mutators
     */
    protected function compileMutators(array &$mutators)
    {
        foreach ($mutators as &$context) {
            $context = new Mutator($context);
        }
    }
}