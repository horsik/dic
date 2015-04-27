<?php

namespace Kampaw\Dic\Instance\Factory;

interface InstanceFactoryInterface
{
    /**
     * @param string $class
     * @param array $arguments
     * @return object
     */
    public function getInstance($class, array $arguments = array());
}