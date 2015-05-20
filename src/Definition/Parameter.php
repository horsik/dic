<?php

namespace Kampaw\Dic\Definition;

class Parameter
{
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
     * @param array $context
     */
    public function __construct(array $context)
    {
        foreach ($context as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        $this->optional = array_key_exists('value', $context);
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
     * @return boolean
     */
    public function isOptional()
    {
        return $this->optional;
    }
}