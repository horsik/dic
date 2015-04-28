<?php

namespace Kampaw\Dic\Definition;

/**
 * @todo(kampaw) refactor i don't like it
 */
class DefinitionContainer
{
    /**
     * @var Definition[][] $concrete
     */
    protected $concrete = array();

    /**
     * @var Definition[][] $abstract
     */
    protected $abstract = array();

    /**
     * @var Definition[] $name
     */
    protected $name = array();

    /**
     * @param string $name
     * @returns Definition|null
     */
    public function getByName($name)
    {
        if (isset($this->name[$name])) {
            return $this->name[$name];
        }
    }

    /**
     * @param string $type
     * @returns Definition|null
     */
    public function getByType($type)
    {
        if (isset($this->concrete[$type])) {
            return current($this->concrete[$type]);
        }
        elseif (isset($this->abstract[$type])) {
            return current($this->abstract[$type]);
        }
    }

    /**
     * @param Definition $definition
     * @returns bool
     */
    public function insert(Definition $definition)
    {
        if (isset($this->name[$definition->getName()])) {
            return false;
        }

        $this->addConcrete($definition);
        $this->addAbstract($definition);
        $this->addName($definition);

        return true;
    }

    /**
     * @param Definition $definition
     * @returns bool
     */
    public function remove(Definition $definition)
    {
        // not needed at this point
        // also it's highly questionable if clear() will ever be
    }

    /**
     * @returns int
     */
    public function count()
    {
        $count = 0;

        foreach ($this->concrete as $value) {
            $count += count($value);
        }

        return $count;
    }

    /**
     * @returns null
     */
    public function clear()
    {
        $this->concrete = array();
        $this->abstract = array();
        $this->name = array();
    }

    /**
     * @param Definition $definition
     */
    protected function addConcrete(Definition $definition)
    {
        $concrete = $definition->getConcrete();

        $this->concrete[$concrete][] = $definition;
    }

    /**
     * @param Definition $definition
     */
    protected function addAbstract(Definition $definition)
    {
        $abstract = $definition->getAbstract();

        if (!empty($abstract)) {
            $this->abstract[$abstract][] = $definition;
        }
    }

    /**
     * @param Definition $definition
     */
    protected function addName(Definition $definition)
    {
        $name = $definition->getName();

        if (!empty($name)) {
            $this->name[$name] = $definition;
        }
    }
}