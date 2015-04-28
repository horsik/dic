<?php

namespace Kampaw\Dic\Definition;

class DefinitionFactory
{
    /**
     * @var \ReflectionClass $reflection
     */
    protected $reflection;

    /**
     * @param string $class
     * @throws \ReflectionException
     * @return array
     */
    public function getDefinition($class)
    {
        $this->reflection = new \ReflectionClass($class);

        return array(
            'concrete' => $this->getClass(),
            'parameters' => $this->getParameters(),
            'mutators' => $this->getMutators(),
        );
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return '\\' . $this->reflection->getName();
    }

    /**
     * @return array
     */
    protected function getParameters()
    {
        $result = array();

        $constructor = $this->reflection->getConstructor();

        if (!$constructor) {
            return $result;
        }

        foreach ($constructor->getParameters() as $parameter) {
            $config = array();

            if ($class = $parameter->getClass()) {
                $config['type'] = '\\' . $class->getName();
            }

            if ($parameter->isOptional()) {
                $config['value'] = $parameter->getDefaultValue();
            }

            $config['name'] = $parameter->getName();

            $result[] = $config;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getMutators()
    {
        $result = array();

        $methods = $this->reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $config = array();

            $name = $method->getName();
            $parameters = $method->getParameters();

            if (strncasecmp($name, 'set', 3) <> 0) {
                /* method name without a prefix, discard */
                continue;
            }

            if (empty($parameters)) {
                /* method with no parameters, discard */
                continue;
            }

            if ($class = $parameters[0]->getClass()) {
                $config['type'] = '\\' . $class->getName();
            }

            $config['name'] = lcfirst(substr($name, 3));

            $result[] = $config;
        }

        return $result;
    }
}