<?php

namespace Kampaw\Dic\Definition\Parameter;

use Kampaw\Dic\Exception\InvalidArgumentException;

abstract class AbstractParameter
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
        $this->setName($name);

        if (!is_null($type)) {
            $this->setType($type);
        }

        if (func_num_args() > 2) {
            $this->setValue($value);
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
     * @param string $name
     */
    protected abstract function setName($name);

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    protected abstract function setType($type);

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    protected abstract function setValue($value);

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
        $type = $this->getType();

        return !$type
            or is_a($value, $type)
            or ($type === 'array' and is_array($value));
    }
}