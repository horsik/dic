<?php

namespace Kampaw\Dic\Definition\InterfaceDefinition;

class UnsanitizedInterfaceDefinition extends AbstractInterfaceDefinition
{
    /**
     * @param string $interface
     */
    protected function setInterface($interface)
    {
        $this->interface = $interface;
    }

    /**
     * @param $candidate
     */
    protected function addCandidate($candidate)
    {
        $this->candidates[] = $candidate;
    }
}