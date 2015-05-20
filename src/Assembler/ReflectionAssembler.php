<?php

namespace Kampaw\Dic\Assembler;

class ReflectionAssembler implements AssemblerInterface
{
    /**
     * @param string $class
     * @param array $arguments
     * @return object
     */
    public function getInstance($class, array $arguments = array())
    {
        $reflection = new \ReflectionClass($class);

        return $reflection->newInstanceArgs($arguments);
    }
}