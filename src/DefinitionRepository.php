<?php

namespace Kampaw\Dic;

use Kampaw\Dic\Definition\AbstractDefinition;

class DefinitionRepository
{
    /**
     * @var AbstractDefinition[] $types
     */
    protected $types = array();

    /**
     * @var AbstractDefinition[] $names
     */
    protected $names = array();

    /**
     * @param $type
     * @return bool
     */
    public function hasType($type)
    {
        return isset($this->types[$type]);
    }

    /**
     * @param string $type
     * @return AbstractDefinition
     */
    public function getByType($type)
    {
        return $this->types[$type];
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasName($name)
    {
        return isset($this->names[$name]);
    }

    /**
     * @param string $name
     * @return AbstractDefinition
     */
    public function getByName($name)
    {
        return $this->names[$name];
    }

    /**
     * @param AbstractDefinition $definition
     */
    public function insert(AbstractDefinition $definition)
    {
        $this->addConcrete($definition);
        $this->addAbstract($definition);
        $this->addName($definition);
    }

    /**
     * @param AbstractDefinition $definition
     */
    protected function addConcrete(AbstractDefinition $definition)
    {
        $concrete = $definition->getConcrete();

        if (!isset($this->types[$concrete])) {
            $this->types[$concrete] = $definition;
        }
    }

    /**
     * @param AbstractDefinition $definition
     */
    protected function addAbstract(AbstractDefinition $definition)
    {
        $abstract = $definition->getAbstract();

        if ($abstract && !isset($this->types[$abstract])) {
            $this->types[$abstract] = $definition;
        }
    }

    /**
     * @param AbstractDefinition $definition
     */
    protected function addName(AbstractDefinition $definition)
    {
        $name = $definition->getName();

        if ($name && !isset($this->names[$name])) {
            $this->names[$name] = $definition;
        }
    }
}