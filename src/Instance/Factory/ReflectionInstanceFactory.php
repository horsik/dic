<?php

namespace Kampaw\Dic\Instance\Factory;

class ReflectionInstanceFactory implements InstanceFactoryInterface
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