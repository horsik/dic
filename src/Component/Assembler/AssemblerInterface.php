<?php

namespace Kampaw\Dic\Component\Assembler;

interface AssemblerInterface
{
    /**
     * @param string $class
     * @param array $arguments
     * @return object
     */
    public function getInstance($class, array $arguments = array());
}