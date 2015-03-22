<?php

namespace Kampaw\Dic\Definition;

class InterfaceDefinition implements DefinitionInterface
{
    protected $interface;
    protected $candidates;

    protected $index;

    public function __construct($interface, array $candidates)
    {
        $this->interface = $interface;
        $this->candidates = new \SplDoublyLinkedList();
    }

    /**
     * @return string
     */
    public function getInterface()
    {
        return $this->interface;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->abstract;
    }

    /**
     * @return array[Parameter]
     */
    public function getParameters()
    {
        // TODO: Implement getParameters() method.
    }

    public function addDefinition()
    {

    }

    public function setCandidate()
    {

    }
}