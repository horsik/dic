<?php

namespace Kampaw\Dic\Definition;

use Kampaw\Dic\Exception\InvalidArgumentException;

class Parameter
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var mixed $value
     */
    protected $value;

    /**
     * @var bool $optional
     */
    protected $optional = false;

    /**
     * @param string $name
     * @param string|null $type
     * @param mixed|null $value
     */
    public function __construct($name, $type = null, $value = null)
    {
        $this->name = $name;
        $this->type = $type;

        if (func_num_args() > 2) {
            $this->value = $value;
            $this->optional = true;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isOptional()
    {
        return $this->optional;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function acceptsType($type)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException('Parameter type must be a string');
        }

        return interface_exists($type)
            or class_exists($type)
            or $type === 'array';
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function acceptsValue($value)
    {
        return !$this->type
            or is_a($value, $this->type)
            or ($this->type === 'array' and is_array($value));
    }
}