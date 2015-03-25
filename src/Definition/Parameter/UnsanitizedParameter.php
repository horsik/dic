<?php

namespace Kampaw\Dic\Definition\Parameter;

class UnsanitizedParameter extends AbstractParameter
{
    /**
     * @param string $name
     */
    protected function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $type
     */
    protected function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $value
     */
    protected function setValue($value)
    {
        $this->value = $value;
    }
}