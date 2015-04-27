<?php

namespace Kampaw\Dic\Instance\Factory;

class UnpackingInstanceFactory implements InstanceFactoryInterface
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