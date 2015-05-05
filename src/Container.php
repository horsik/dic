<?php

namespace Kampaw\Dic;

use Kampaw\Dic\Definition\DefinitionContainer;
use Kampaw\Dic\Definition\DefinitionInterface;
use Kampaw\Dic\Definition\Parameter;
use Kampaw\Dic\Component\Assembler\AssemblerInterface;

final class Container
{
    /**
     * @var DefinitionContainer $definitions
     */
    protected $definitions;

    /**
     * @var \splObjectStorage $components
     */
    protected $components;

    /**
     * @var AssemblerInterface $assembler
     */
    protected $assembler;

    /**
     * @var bool $discovery
     */
    protected $discovery;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->definitions = new DefinitionContainer();
        $this->components = new \SplObjectStorage();

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
//        $definition = $this->getDefinition($type);
//
//        return $this->resolveDefinition($definition);
    }

    /**
     * @param object $object
     */
    public function inject($object)
    {

    }

    /**
     * @param AssemblerInterface $type
     */
    protected function setAssembler(AssemblerInterface $type)
    {
        $this->assembler = $type;
    }

    /**
     * @param array $definitions
     */
    protected function setDefinitions(array $definitions)
    {
        foreach ($definitions as $definition) {
//            $this->definitionContainer->insert($definition);
        }
    }

    /**
     * @param string $type
     * @throws Exception\ComponentCreationException
     * @return DefinitionInterface
     */
    private function getDefinition($type)
    {
//        if ($definition = $this->definitionContainer->getByType($type)) {
//            return $definition;
//        }
//
//        if (!$this->definitionFactory) {
//            $msg = "DefinitionInterface $type missing from repository and cannot be resolved automatically. "
//                 . "Enable auto discovery or provide valid definition in the configuration";
//
//            throw new Exception\ComponentCreationException($msg);
//        }
//
//        if ($definition = $this->discoverDefinition($type)) {
//            return $definition;
//        }
    }

    /**
     * @param $type
     * @throws Exception\DefinitionDiscoveryException
     * @return DefinitionInterface
     */
    private function discoverDefinition($type)
    {
//        try {
//            $definition = new RuntimeDefinition($type);
//        } catch (\ReflectionException $e) {
//            $parent = $type; // todo(kampaw) parent class in exception message
//            $msg = "Auto discovery failed while instantiating $parent, definition for class $type "
//                 . "cannot be constructed. Check $type constructor/mutators for typos and ensure that "
//                 . "autoloader is correctly configured. Previous exception was: {$e->getMessage()}";
//
//            throw new Exception\DefinitionDiscoveryException($msg);
//        }
//
//        $this->definitionContainer->insert($definition);
//
//        return $definition;
    }

    /**
     * @param DefinitionInterface $definition
     * @return object
     */
    private function resolveDefinition(DefinitionInterface $definition)
    {
//        $arguments = $this->getArguments($definition);
//
//        return $this->instanceFactory->getInstance($definition->getConcrete(), $arguments);
    }

    /**
     * @param DefinitionInterface $definition
     * @returns object[]
     */
    private function getArguments(DefinitionInterface $definition)
    {
//        $arguments = array();
//
//        $parameters = $definition->getParameters();
//
//        foreach ($parameters as $parameter) {
//            $arguments[] = $this->getArgument($parameter);
//        }
//
//        return $arguments;
    }

    /**
     * @param Parameter $parameter
     * @returns object
     */
    private function getArgument(Parameter $parameter)
    {
//        $definition = $this->getDefinition($parameter->getType());
//
//        return $this->resolveDefinition($definition);
    }
}