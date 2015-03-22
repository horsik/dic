<?php

namespace Kampaw\Dic\Definition;

use Kampaw\Dic\Exception\DomainException;
use Kampaw\Dic\Exception\InvalidArgumentException;

class SanitizedParameter extends Parameter
{
    /**
     * @param Parameter $parameter
     */
    public function __construct(Parameter $parameter)
    {
        $this->setName($parameter->getName());

        if (!is_null($parameter->getType())) {
            $this->setType($parameter->getType());
        }

        if ($parameter->isOptional()) {
            $this->setValue($parameter->getValue());
            $this->optional = true;
        }
    }

    /**
     * @param string $name
     */
    protected function setName($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Parameter name must be a string');
        }

        if (empty($name)) {
            throw new DomainException('Parameter name cannot be empty');
        }

        $this->name = $name;
    }

    /**
     * @param string $type
     */
    protected function setType($type)
    {
        if (!$this->acceptsType($type)) {
            throw new DomainException("$type is not a valid type hint or cannot be autoloaded");
        }

        $this->type = $type;
    }

    /**
     * @param mixed $value
     */
    protected function setValue($value)
    {
        if ($this->type and !$this->acceptsValue($value)) {
            throw new InvalidArgumentException("Invalid type {$this->type} expected");
        }

        $this->value = $value;
    }
}