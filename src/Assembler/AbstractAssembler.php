<?php

namespace Kampaw\Dic\Assembler;

use Kampaw\Dic\Definition\DefinitionInterface;
use Kampaw\Dic\Definition\Parameter;
use Kampaw\Dic\DefinitionRepository;
use Kampaw\Dic\Exception\BadMethodCallException;
use Kampaw\Dic\Exception\CircularDependencyException;
use Kampaw\Dic\Exception\InvalidArgumentException;
use Kampaw\Dic\Exception\LogicException;

abstract class AbstractAssembler implements AssemblerInterface
{
    /**
     * @var DefinitionRepository $definitions
     */
    protected $definitions;

    /**
     * @var \SplObjectStorage $safeguard
     */
    protected $safeguard;

    /**
     * @param DefinitionRepository $definitions
     */
    public function __construct(DefinitionRepository $definitions)
    {
        $this->definitions = $definitions;
        $this->safeguard = new \SplObjectStorage();
    }

    /**
     * @param string $class
     * @param array $arguments
     * @return object
     */
    public function assemble($class, array $arguments = array())
    {
        $definition = $this->definitions->get($class);

        if (!$definition) {
            throw new LogicException("$class definition missing from repository");
        }

        if ($this->safeguard->contains($definition)) {
            $parent = $this->getConstructedClass();
            throw new CircularDependencyException("Circular dependency $class detected in $parent");
        }

        $this->safeguard->attach($definition);

        $arguments = $this->getDependencies($definition, $arguments);
        $instance = $this->createInstance($definition, $arguments);

        $this->safeguard->detach($definition);

        return $instance;
    }

    /**
     * @param DefinitionInterface $class
     * @param array $arguments
     * @return object
     */
    abstract protected function createInstance(DefinitionInterface $class, array $arguments);

    /**
     * @param DefinitionInterface $definition
     * @param array $arguments[Parameter]
     * @return array
     */
    protected function getDependencies(DefinitionInterface $definition, array $arguments = array())
    {
        $dependecies = array();
        $parameters = $definition->getParameters();

        $arguments = $this->supplementParameterNames($parameters, $arguments);

        foreach ($parameters as $parameter) {
            $dependecies[] = $this->getDependency($parameter, $arguments);
        }

        return $dependecies;
    }

    /**
     * @param Parameter[] $parameters
     * @param $arguments
     * @return mixed
     */
    protected function supplementParameterNames(array $parameters, array $arguments)
    {
        $index = 0;

        foreach ($arguments as $key => $value) {
            if (is_int($key)) {
                unset($arguments[$key]);

                $arguments[$parameters[$index]->getName()] = $value;
            }

            $index++;
        }

        return $arguments;
    }

    /**
     * @param Parameter $parameter
     * @param array $arguments
     * @return mixed
     */
    protected function getDependency(Parameter $parameter, array $arguments)
    {
        $name = $parameter->getName();

        if (array_key_exists($name, $arguments)) {
            if (!$parameter->acceptsValue($arguments[$name])) {
                throw new InvalidArgumentException();
            }

            return $arguments[$name];
        }

        if ($parameter->getType()) {
            return $this->assemble($parameter->getType());
        }

        if ($parameter->isOptional()) {
            return $parameter->getValue();
        }

        throw new BadMethodCallException(
            "Cannot satisfy dependency $$name for class " . $this->getConstructedClass()
        );
    }

    /**
     * @return string
     */
    protected function getConstructedClass()
    {
        $iterator = new \LimitIterator($this->safeguard);
        $iterator->rewind();
        $iterator->seek($this->safeguard->count() - 1);

        return $iterator->current()->getClass();
    }
}