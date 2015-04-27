<?php

namespace Kampaw\Dic;

use Kampaw\Dic\Config\Configurable;
use Kampaw\Dic\Definition\Definition;
use Kampaw\Dic\Definition\Parameter;
use Kampaw\Dic\Definition\DefinitionContainer;
use Kampaw\Dic\Definition\DefinitionFactory;
use Kampaw\Dic\Exception\ComponentCreationException;
use Kampaw\Dic\Exception\DefinitionDiscoveryException;
use Kampaw\Dic\Exception\InvalidDefinitionException;
use Kampaw\Dic\Instance\Factory\InstanceFactoryInterface;
use Kampaw\Dic\Instance\Factory\ReflectionInstanceFactory;
use Kampaw\Dic\Instance\Factory\SmartInstanceFactory;
use Kampaw\Dic\Instance\Factory\UnpackingInstanceFactory;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class Dic extends Configurable
{
    /**
     * @var DefinitionContainer $definitionContainer
     */
    private $definitionContainer;

    /**
     * @var DefinitionFactory $definitionFactory
     */
    private $definitionFactory;

    /**
     * @var \splObjectStorage $componentContainer
     */
    private $componentContainer;

    /**
     * @var InstanceFactoryInterface $instanceFactory
     */
    private $instanceFactory;

    /**
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->definitionContainer = new DefinitionContainer();
        $this->componentContainer = new \SplObjectStorage();

        Configurable::__construct($config);
    }

    /**
     * @param $type
     * @return object
     */
    public function get($type)
    {
        $definition = $this->getDefinition($type);

        return $this->resolveDefinition($definition);
    }

    /**
     * @param object $object
     */
    public function inject($object)
    {

    }

    /**
     * @param string $type
     */
    protected function setInstanceFactory($type)
    {
        switch ($type) {
            case 'reflection':
                $this->instanceFactory = new ReflectionInstanceFactory();
                break;
            case 'unpacking':
                $this->instanceFactory = new UnpackingInstanceFactory();
                break;
            case 'smart':
            default:
                $this->instanceFactory = new SmartInstanceFactory();
        }
    }

    /**
     * @param bool $enabled
     */
    protected function setAutoDiscover($enabled)
    {
        if ($enabled) {
            $this->definitionFactory = new DefinitionFactory();
        }
    }

    /**
     * @param array $configs
     * @throws InvalidDefinitionException
     */
    protected function setDefinitions(array $configs)
    {
        foreach ($configs as $key => $value) {
            /**
             * 1. check definition
             * 2. if invalid log errors and throw exception
             * 3. compile definition
             * 4. add definition
             */

//            $this->definitionContainer->insert($definition);
        }
    }

    /**
     * @param string $type
     * @throws ComponentCreationException
     * @return Definition
     */
    protected function getDefinition($type)
    {
        if ($definition = $this->definitionContainer->getByType($type)) {
            return $definition;
        }

        if (!$this->definitionFactory) {
            $msg = "Definition $type missing from repository and cannot be resolved automatically. "
                 . "Enable auto discovery or provide valid definition in the configuration";

            throw new ComponentCreationException($msg);
        }

        if ($definition = $this->discoverDefinition($type)) {
            return $definition;
        }
    }

    /**
     * @param $type
     * @throws DefinitionDiscoveryException
     * @return Definition
     */
    protected function discoverDefinition($type)
    {
        try {
            $definition = $this->definitionFactory->getDefinition($type);
        } catch (\ReflectionException $e) {
            $parent = $type; // todo(kampaw) parent class in exception message
            $msg = "Auto discovery failed while instantiating $parent, definition for class $type "
                 . "cannot be constructed. Check $type constructor/mutators for typos and ensure that "
                 . "autoloader is correctly configured. Previous exception was: {$e->getMessage()}";

            throw new DefinitionDiscoveryException($msg);
        }

        $this->definitionContainer->insert($definition);

        return $definition;
    }

    /**
     * @param Definition $definition
     * @return object
     */
    protected function resolveDefinition(Definition $definition)
    {
        $arguments = $this->getArguments($definition);

        return $this->instanceFactory->getInstance($definition->getConcrete(), $arguments);
    }

    /**
     * @param Definition $definition
     * @returns object[]
     */
    protected function getArguments(Definition $definition)
    {
        $arguments = array();

        foreach ($definition->getParameters() as $parameter) {
            $arguments[] = $this->getArgument($parameter);
        }

        return $arguments;
    }

    /**
     * @param Parameter $parameter
     * @returns object
     */
    protected function getArgument(Parameter $parameter)
    {
        $definition = $this->getDefinition($parameter->getType());

        return $this->resolveDefinition($definition);
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger()
    {
        if (!$this->logger) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    private function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}