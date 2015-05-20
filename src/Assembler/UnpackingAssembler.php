<?php

namespace Kampaw\Dic\Assembler;

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