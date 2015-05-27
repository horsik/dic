<?php

namespace Kampaw\Dic\Definition;

abstract class AbstractDefinition
{
    /**
     * @var string $concrete
     */
    protected $concrete;

    /**
     * @var string $abstract
     */
    protected $abstract;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var Parameter[] $parameters
     */
    protected $parameters = array();

    /**
     * @var Mutator[] $mutators
     */
    protected $mutators = array();

    /**
     * @var string $lifetime
     */
    protected $lifetime = 'transient';

    /**
     * @var string $autowire
     */
    protected $autowire = AutowireMode::AUTODETECT;

    /**
     * @var bool $candidate
     */
    protected $candidate = true;

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
    public function getConcrete()
    {
        return $this->concrete;
    }

    /**
     * @return string
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @return Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return Mutator[]
     */
    public function getMutators()
    {
        return $this->mutators;
    }

    /**
     * @return string
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * @return string
     */
    public function getAutowire()
    {
        return $this->autowire;
    }

    /**
     * @return boolean
     */
    public function isCandidate()
    {
        return $this->candidate;
    }
}