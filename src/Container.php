<?php

namespace Kampaw\Dic;

use Kampaw\Dic\Assembler\AssemblerInterface;
use Kampaw\Dic\Definition\AbstractDefinition;
use Kampaw\Dic\Definition\ArrayDefinition;
use Kampaw\Dic\Definition\AutowireMode;
use Kampaw\Dic\Definition\DefinitionException;
use Kampaw\Dic\Definition\RuntimeDefinition;
use Kampaw\Dic\Definition\Parameter;
use Kampaw\Dic\Exception\DependencyException;

class Container
{
    /**
     * @var DefinitionRepository $definitions
     */
    protected $definitions;

    /**
     * @var AssemblerInterface $assembler
     */
    protected $assembler;

    /**
     * @var bool $discovery
     */
    protected $discovery;

    /**
     * @var \SplStack $failsafe
     */
    protected $failsafe;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->definitions = new DefinitionRepository();
        $this->failsafe = new \SplStack();

        $this->discovery = $config['discovery'];
        $this->assembler = $config['assembler'];

        $this->setDefinitions($config['definitions']);
    }

    /**
     * @param $type
     * @return object
     */
    public function get($type)
    {
        $definition = $this->getDefinition($type);

        if (!$definition) {
            $msg = "Definition for type $type is missing from repository. Provide valid definition "
                . "in the configuration or enable automatic definition discovery";

            throw new DefinitionException($msg, 0);
        }

        return $this->resolveDefinition($definition);
    }

    /**
     * @param object $object
     */
    public function inject($object)
    {

    }

    /**
     * @param array $contexts
     */
    protected function setDefinitions(array $contexts)
    {
        foreach ($contexts as $context) {
            $definition = new ArrayDefinition($context);

            $this->definitions->insert($definition);
        }
    }

    /**
     * @param string $type
     * @return AbstractDefinition
     */
    protected function getDefinition($type)
    {
        if ($this->definitions->hasType($type)) {
            $definition = $this->definitions->getByType($type);
        }
        elseif ($this->discovery) {
            $definition = $this->discoverDefinition($type);
        }
        else {
            $definition = null;
        }

        return $definition;
    }

    /**
     * @param AbstractDefinition $definition
     * @return object
     */
    protected function resolveDefinition(AbstractDefinition $definition)
    {
        $this->failsafe->push($definition);

        $arguments = $this->getArguments($definition);
        $instance = $this->assembler->getInstance($definition->getConcrete(), $arguments);

        return $instance;
    }

    /**
     * @param AbstractDefinition $definition
     * @return object
     */
    protected function resolveAutowiredDefinition(AbstractDefinition $definition)
    {
        if ($definition->isCandidate()) {
            $instance = $this->resolveDefinition($definition);
        }
        else {
            $concrete = $definition->getConcrete();
            $msg = "Definition for type $concrete is excluded from autowiring. Remove override or "
                . "explicitly set a reference to another definition";

            throw new DependencyException($msg, 10);
        }

        return $instance;
    }

    /**
     * @param string $type
     * @return RuntimeDefinition
     */
    protected function discoverDefinition($type)
    {
        try {
            $definition = new RuntimeDefinition($type);
        } catch (\Exception $e) {
            $msg = "Automatic discovery failed while creating a definition for type $type. Examine "
                 . "class for errors and ensure that autoloader is correctly configured. See "
                 . "previous exception for details";

            throw new DefinitionException($msg, 10, $e);
        }

        $this->definitions->insert($definition);

        return $definition;
    }

    /**
     * @param AbstractDefinition $definition
     * @return array
     */
    protected function getArguments(AbstractDefinition $definition)
    {
        $arguments = array();

        foreach ($definition->getParameters() as $parameter) {
            $arguments[] = $this->getArgument($parameter);
        }

        return $arguments;
    }

    /**
     * @param Parameter $parameter
     * @return mixed
     */
    protected function getArgument(Parameter $parameter)
    {
        if ($parameter->getRef()) {
            $argument = $this->getArgumentByReference($parameter);
        }
        elseif ($parameter->getType()) {
            $argument = $this->getArgumentByType($parameter);
        }
        elseif ($parameter->isOptional()) {
            $argument = $parameter->getValue();
        }
        else {
            $concrete = $this->failsafe->top()->getConcrete();
            $msg = "Malformed parameter in definition $concrete. Parameter has no type nor default "
                 . "value. Use ContainerBuilder API to manually create definitions and validate "
                 . "external configuration files";

            throw new DefinitionException($msg, 20);
        }

        return $argument;
    }

    /**
     * @param Parameter $parameter
     * @return object
     */
    protected function getArgumentByReference(Parameter $parameter)
    {
        $ref = $parameter->getRef();

        if ($this->definitions->hasName($ref)) {
            $definition = $this->definitions->getByName($ref);
            $argument = $this->resolveDefinition($definition);
        }
        else {
            $msg = "Invalid reference to definition $ref, no definition provided in configuration "
                 . "matches requested name";

            throw new DefinitionException($msg, 30);
        }

        return $argument;
    }

    /**
     * @param Parameter $parameter
     * @return object
     */
    protected function getArgumentByType(Parameter $parameter)
    {
        $definition = $this->getDefinition($parameter->getType());
        $autowire = $this->failsafe->top()->getAutowire();

        if ($definition && $autowire & AutowireMode::CONSTRUCTOR) {
            $argument = $this->resolveAutowiredDefinition($definition);
        }
        elseif ($parameter->isOptional()) {
            $argument = $parameter->getValue();
        }
        else {
            $type = $parameter->getType();
            $msg = "Parameter of type $type has no default value and couldn't be resolved though "
                 . "autowiring. Specify a reference explicitly in the configuration";

            throw new DependencyException($msg, 0);
        }

        return $argument;
    }
}