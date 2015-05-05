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
    }

    /**
     * @param array $parameters
     */
    protected function setParameters($parameters)
    {
        foreach ($parameters as $parameter) {
            $this->parameters[] = new Parameter($parameter);
        }
    }

    /**
     * @param array $mutators
     */
    protected function setMutators(array $mutators)
    {
        foreach ($mutators as $mutator) {
            $this->mutators[] = new Mutator($mutator);
        }
    }
}