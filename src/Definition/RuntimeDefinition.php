<?php

namespace Kampaw\Dic\Definition;

class RuntimeDefinition extends AbstractDefinition
{
    /**
     * @var \ReflectionClass $reflection
     */
    protected $reflection;

    /**
     * @param string $class
     */
    public function __construct($class)
    {
        $this->reflection = new \ReflectionClass($class);

        $this->setConcrete();
        $this->setParameters();
        $this->setMutators();
    }

    protected function setConcrete()
    {
        $this->concrete = '\\' . $this->reflection->getName();
    }

    protected function setParameters()
    {
        $this->parameters = array();

        $constructor = $this->reflection->getConstructor();

        if (!$constructor) {
            return;
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

            $this->parameters[] = new Parameter($config);
        }
    }

    protected function setMutators()
    {
        $this->mutators = array();

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

            $this->mutators[] = new Mutator($config);
        }
    }
}