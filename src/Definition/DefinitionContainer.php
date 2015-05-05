<?php

namespace Kampaw\Dic\Definition;

class DefinitionContainer
{
    /**
     * @var DefinitionInterface[] $types
     */
    protected $types = array();

    /**
     * @var DefinitionInterface[] $names
     */
    protected $names = array();

    /**
     * @param string $type
     * @return DefinitionInterface
     */
    public function getByType($type)
    {
        if (isset($this->types[$type])) {
            return $this->types[$type];
        }
    }

    /**
     * @param string $name
     * @return DefinitionInterface
     */
    public function getByName($name)
    {
        if (isset($this->names[$name])) {
            return $this->names[$name];
        }
    }

    /**
     * @param DefinitionInterface $definition
     */
    public function insert(DefinitionInterface $definition)
    {
        $this->addConcrete($definition);
        $this->addAbstract($definition);
        $this->addName($definition);
    }

    /**
     * @param DefinitionInterface $definition
     */
    protected function addConcrete(DefinitionInterface $definition)
    {
        $concrete = $definition->getConcrete();

        if (!isset($this->types[$concrete])) {
            $this->types[$concrete] = $definition;
        }
    }

    /**
     * @param DefinitionInterface $definition
     */
    protected function addAbstract(DefinitionInterface $definition)
    {
        $abstract = $definition->getAbstract();

        if ($abstract && !isset($this->types[$abstract])) {
            $this->types[$abstract] = $definition;
        }
    }

    /**
     * @param DefinitionInterface $definition
     */
    protected function addName(DefinitionInterface $definition)
    {
        $name = $definition->getName();

        if ($name && !isset($this->names[$name])) {
            $this->names[$name] = $definition;
        }
    }
}