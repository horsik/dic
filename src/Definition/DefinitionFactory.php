<?php

namespace Kampaw\Dic\Definition;

use Kampaw\Dic\Definition\Definition;
use Kampaw\Dic\Definition\Parameter;

class DefinitionFactory
{
    /**
     * @var \ReflectionClass $reflection
     */
    protected $reflection;

    /**
     * @param string $class
     * @throws \ReflectionException
     * @return Definition
     */
    public function getDefinition($class)
    {
        $this->reflection = new \ReflectionClass($class);

        $config = array(
            'concrete' => $this->getClass(),
            'parameters' => $this->getParameters(),
        );

        return new Definition($config);
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return '\\' . $this->reflection->getName();
    }

    /**
     * @return Parameter[]
     */
    protected function getParameters()
    {
        $result = array();
        $constructor = $this->reflection->getConstructor();

        if (!$constructor) {
            return $result;
        }

        $parameters = $constructor->getParameters();

        foreach ($parameters as $value) {
            $config = array(
                'name' => $value->getName(),
            );

            if ($class = $value->getClass()) {
                $config['type'] = '\\' . $class->getName();
            }

            if ($value->isOptional()) {
                $config['value'] = $value->getDefaultValue();
            }

            $result[] = new Parameter($config);
        }

        return $result;
    }

    /**
     * @return Mutator[]
     */
    protected function getMutators()
    {
        // @todo(kampaw) to be implemented
    }
}