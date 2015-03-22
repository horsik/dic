<?php

namespace Kampaw\Dic\Assembler;

interface AssemblerInterface
{
    /**
     * @param string $class
     * @param array $arguments
     * @return object
     */
    public function assemble($class, array $arguments = array());
}