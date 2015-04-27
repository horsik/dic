<?php

namespace Kampaw\Dic\Definition;

use Kampaw\Dic\Config\Configurable;

class Parameter extends Configurable
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
     * @var string $ref
     */
    protected $ref;

    /**
     * @var bool $optional
     */
    protected $optional;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        Configurable::__construct($config);
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
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @return bool
     */
    public function isOptional()
    {
        return $this->optional;
    }

    /**
     * @param mixed $value
     */
    protected function setValue($value)
    {
        $this->value = $value;
        $this->optional = true;
    }
}