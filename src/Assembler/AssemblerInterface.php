<?php

namespace Kampaw\Dic\Assembler;

interface AssemblerInterface
{
    /**
     * @param string $class
     * @param array $arguments
     * @return object
     */
    public function getInstance($class, array $arguments = array());
}