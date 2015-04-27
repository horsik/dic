<?php

namespace Kampaw\Dic\Instance\Factory;

class SmartInstanceFactory extends ReflectionInstanceFactory
{
    /**
     * @param string $class
     * @param array $arguments
     * @return object
     */
    public function getInstance($class, array $arguments = array())
    {
        switch (count($arguments)) {
            case 0:
                return new $class();
            case 1:
                return new $class($arguments[0]);
            case 2:
                return new $class($arguments[0], $arguments[1]);
            case 3:
                return new $class($arguments[0], $arguments[1], $arguments[2]);
            default:
                return parent::getInstance($class, $arguments);
        }
    }
}