<?php

namespace Kampaw\Dic;

use Kampaw\Dic\Exception\BadMethodCall;
use Kampaw\Dic\Exception\CircularDependencyException;
use Kampaw\Dic\Exception\UnexpectedValueException;

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
     * @return array
     */
    public function listClasses($interface)
    {
        if (isset($this->interfaces[$interface])) {
            return $this->interfaces[$interface];
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

        return $this->getDependency($this->getCandidate($interface));
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
     * @param string $class
     * @throws BadMethodCall
     * @return object
     */
    protected function getDependency($class)
    {
        if ($class) {
            if (array_key_exists($class, $this->singletons)) {
                if (!$singleton = &$this->singletons[$class]) {
                    $singleton = $this->createInstance($class);
                }
                return $singleton;
            } else {
                return $this->createInstance($class);
            }
        } else {
            throw new BadMethodCall("Cannot satisfy dependency $class");
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
                    $candidate = $this->getCandidate($parameter->getClass()->getName());
                    if (in_array($candidate, $this->dependencies)) {
                        throw new CircularDependencyException("Circular dependency $candidate");
                    }
                    $args[] = $this->getDependency($candidate);
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $args[] = $parameter->getDefaultValue();
                } elseif (!$parameter->isOptional()) {
                    throw new BadMethodCall("Cannot satisfy $class dependencies");
                }
            }
        }

        return $reflection->newInstanceArgs($args);
    }
}