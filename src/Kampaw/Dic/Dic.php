<?php

namespace Kampaw\Dic;

use Kampaw\Dic\Exception\BadMethodCall;
use Kampaw\Dic\Exception\CircularDependencyException;
use Kampaw\Dic\Exception\InvalidArgumentException;
use Kampaw\Dic\Exception\UnexpectedValueException;

/**
 * Class Dic
 *
 * @package Kampaw\Dic
 */
class Dic implements DicInterface
{
    /**
     * @var array $interfaces
     */
    protected $interfaces = array();

    /**
     * @var array $singletons
     */
    protected $singletons = array();

    /**
     * @var array $dependencies
     */
    protected $dependencies = array();

    /**
     * @var array $aliases
     */
    protected $aliases = array();

    /**
     * @param $class
     * @param $interface
     * @param bool $shared
     * @param null $instance
     * @return bool
     */
    public function register($class, $interface, $shared = true, $instance = null)
    {
        if (!class_exists($class)) {
            throw new UnexpectedValueException('Supplied class not exists');
        }
        if (!interface_exists($interface)) {
            throw new UnexpectedValueException('Supplied interface not exists');
        }
        if (isset($this->interfaces[$interface]) and in_array($class, $this->interfaces[$interface])) {
            throw new UnexpectedValueException('Class already registred');
        }
        if (!in_array($interface, class_implements($class))) {
            throw new UnexpectedValueException("Class doesn't implement supplied interface");
        }

        $this->interfaces[$interface][] = $class;

        if (!$shared) {
            $this->singletons[$class] = $instance;
        }
    }

    /**
     * @param string $interface
     * @param string $as
     */
    public function registerAlias($interface, $as)
    {
        if (isset($this->interfaces[$interface])) {
            if (!isset($this->aliases[$as])) {
                $this->aliases[$as] = $interface;
            } else {
                throw new UnexpectedValueException('Alias already registred');
            }
        } else {
            throw new UnexpectedValueException('Supplied interface not registred');
        }
    }

    /**
     * @param string $alias
     */
    public function unregisterAlias($alias)
    {
        if (isset($this->aliases[$alias])) {
            unset($this->aliases[$alias]);
        } else {
            throw new UnexpectedValueException('Supplied alias does not exist');
        }
    }

    /**
     * @param string $interface
     * @return array
     */
    public function listClasses($interface)
    {
        if (isset($this->interfaces[$interface])) {
            return $this->interfaces[$interface];
        } elseif (isset($this->aliases[$interface])) {
            return $this->interfaces[$this->aliases[$interface]];
        } else {
            return array();
        }
    }

    /**
     * @param string $interface
     * @return string|null
     */
    public function getCandidate($interface)
    {
        if ($classes = $this->listClasses($interface)) {
            return end($classes);
        }
    }

    /**
     * @param $interface
     * @return object
     */
    public function resolveInterface($interface)
    {
        $this->dependencies = array();

        return $this->getDependency($interface);
    }

    /**
     * @param $class
     * @return object
     */
    public function resolveClass($class)
    {
        $this->dependencies = array();

        return $this->createInstance($class);
    }

    /**
     * @param object $object
     * @return object
     */
    public function injectDependencies($object)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException('Argument must be an object');
        }

        return $this->_injectDependencies($object, new \ReflectionClass($object));
    }

    /**
     * @param $interface
     * @throws CircularDependencyException
     * @return object
     */
    protected function getDependency($interface)
    {
        if ($class = $this->getCandidate($interface)) {
            if (in_array($class, $this->dependencies)) {
                throw new CircularDependencyException("Circular dependency $class");
            }
            if (array_key_exists($class, $this->singletons)) {
                if (!$singleton = &$this->singletons[$class]) {
                    $singleton = $this->createInstance($class);
                }
                return $singleton;
            } else {
                return $this->createInstance($class);
            }
        } else {
            throw new BadMethodCall("Cannot satisfy dependency $interface");
        }
    }

    /**
     * @param $class
     * @throws CircularDependencyException
     * @throws BadMethodCall
     * @return object
     */
    protected function createInstance($class)
    {
        $reflection = new \ReflectionClass($class);
        $args = array();

        $this->dependencies[] = $class;

        if ($constructor = $reflection->getConstructor()) {
            foreach ($constructor->getParameters() as $parameter) {
                if ($parameter->getClass()) {
                    $args[] = $this->getDependency($parameter->getClass()->name);
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $args[] = $parameter->getDefaultValue();
                } elseif (!$parameter->isOptional()) {
                    throw new BadMethodCall("Cannot satisfy $class dependencies");
                }
            }
        }

        return $this->_injectDependencies($reflection->newInstanceArgs($args), $reflection);
    }

    /**
     * @param object $object
     * @param \ReflectionClass $reflection
     * @return object
     */
    protected function _injectDependencies($object, \ReflectionClass $reflection)
    {
        foreach ($reflection->getInterfaces() as $interface) {
            if (strrpos($interface->name, 'AwareInterface')) {
                foreach ($interface->getMethods() as $method) {
                    if (!strncasecmp($method->name, 'set', 3)) {
                        $args = array();
                        foreach ($method->getParameters() as $parameter) {
                            if ($class = $parameter->getClass()) {
                                try {
                                    $args[] = $this->getDependency($class->name);
                                } catch (BadMethodCall $e) {
                                    continue 2;
                                }
                            } else {
                                throw new BadMethodCall("Invalid parameter {$parameter->name}");
                            }
                        }
                        call_user_func_array(array($object, $method->name), $args);
                    }
                }
            }
        }

        return $object;
    }
}