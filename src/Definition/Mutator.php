<?php

namespace Kampaw\Dic\Definition;

use Kampaw\Dic\Config\Configurable;

class Mutator extends Configurable
{
    /**
     * @var string $method
     */
    protected $method;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $ref
     */
    protected $ref;

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
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
    }
}