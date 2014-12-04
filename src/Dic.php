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
    protected $factories = array();

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
     * @var array $candidates
     */
    protected $candidates = array();

    /**
     * @param array $config
     */
    public function __construct($config = null)
    {
        if ($config) {
            $this->setConfig($config);
        }
    }

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
            throw new UnexpectedValueException("Supplied class $class not exists");
        }
        if (!interface_exists($interface)) {
            throw new UnexpectedValueException("Supplied interface $interface not exists");
        }
        if (in_array($class, $this->listClasses($interface))) {
            throw new UnexpectedValueException("Class $class already registred");
        }
        if (!in_array($interface, class_implements($class))) {
            throw new UnexpectedValueException("Class $class doesn't implement interface $interface");
        }

        $this->candidates[$interface] = $class;

        if ($shared) {
            $this->factories[$interface][] = $class;
        } else {
            $this->singletons[$interface][$class] = $instance;
        }
    }

    /**
     * @param array $group
     * @param bool $shared
     */
    public function registerGroup(array $group, $shared = false)
    {
        foreach ($group as $interface => $classes) {
            foreach ((array) $classes as $class) {
                $this->register($class, $interface, $shared);
            }
        }
    }

    /**
     * @param string $interface
     * @param string $as
     */
    public function registerAlias($interface, $as)
    {
        if (!is_string($as)) {
            throw new InvalidArgumentException('Alias must be a string');
        }

        if ($this->getCandidate($interface)) {
            if (!isset($this->aliases[$as])) {
                $this->aliases[$as] = $interface;
            } else {
                throw new UnexpectedValueException("Alias $as already registred");
            }
        } else {
            throw new UnexpectedValueException("Supplied interface $interface not registred");
        }
    }

    /**
     * @param array $config
     */
    public function registerAliasGroup(array $config)
    {
        foreach ($config as $interface => $alias) {
            $this->registerAlias($interface, $alias);
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
            throw new UnexpectedValueException("Supplied alias $alias does not exist");
        }
    }

    /**
     * @param string $interface
     * @return array
     */
    public function listClasses($interface)
    {
        $interfaces = array();

        if (isset($this->factories[$interface])) {
            $interfaces += $this->factories[$interface];
        }
        if (isset($this->singletons[$interface])) {
            $interfaces += array_keys($this->singletons[$interface]);
        }

        return $interfaces;
    }

    /**
     * @param string $interface
     * @return string|null
     */
    public function getCandidate($interface)
    {
        if (isset($this->candidates[$interface])) {
            return $this->candidates[$interface];
        }
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $singletons = array();

        foreach ($this->singletons as $interface => $classes) {
            $singletons[$interface] = array_keys($classes);
        }

        return array(
            'factories' => $this->factories,
            'singletons' => $singletons,
            'aliases' => array_flip($this->aliases),
        );
    }

    /**
     * @param mixed $config
     */
    public function setConfig($config)
    {
        $config = (array) $config;

        if (isset($config['factories'])) {
            $this->factories = array();
            $this->registerGroup($config['factories'], true);
        }

        if (isset($config['singletons'])) {
            $this->singletons = array();
            $this->registerGroup($config['singletons'], false);
        }

        if (isset($config['aliases'])) {
            $this->aliases = array();
            $this->registerAliasGroup($config['aliases']);
        }
    }

    /**
     * @param string $name
     * @return object
     */
    public function resolve($name)
    {
        if (interface_exists($name)) {
            return $this->resolveInterface($name);
        } elseif (isset($this->aliases[$name])) {
            return $this->resolveInterface($this->aliases[$name]);
        } else {
            return $this->resolveClass($name);
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
            if (isset($this->singletons[$interface])
                and array_key_exists($class, $this->singletons[$interface]))
            {
                if (!$singleton = &$this->singletons[$interface][$class]) {
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