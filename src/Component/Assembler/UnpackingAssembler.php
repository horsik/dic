<?php

namespace Kampaw\Dic\Component\Assembler;

class UnpackingAssembler implements AssemblerInterface
{
    /**
     * @param string $class
     * @param array $arguments
     * @return object
     */
    public function getInstance($class, array $arguments = array())
    {
        return new $class(...$arguments);
    }
}