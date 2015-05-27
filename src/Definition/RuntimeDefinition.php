<?php

namespace Kampaw\Dic\Definition;

class RuntimeDefinition extends AbstractDefinition
{
    /**
     * @param string $class
     */
    public function __construct($class)
    {
        $reflection = new \ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            $msg = "Unable to create a runtime definition for $class, class is not instantiable";

            throw new DefinitionException($msg);
        }

        $this->setConcrete($reflection);
        $this->setParameters($reflection);
        $this->setMutators($reflection);
    }

    /**
     * @param \ReflectionClass $reflection
     */
    protected function setConcrete(\ReflectionClass $reflection)
    {
        $this->concrete = $reflection->getName();
    }

    /**
     * @param \ReflectionClass $reflection
     */
    protected function setParameters(\ReflectionClass $reflection)
    {
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            /* no parameters, nothing to do here */
            return;
        }

        foreach ($constructor->getParameters() as $parameter) {
            $context = $this->getParameterContext($parameter);

            if ($context) {
                $this->parameters[] = new Parameter($context);
            }
            else {
                $name = $parameter->getName();
                $concrete = $reflection->getName();

                $msg = "Unable to create a runtime definition for $concrete, constructor parameter "
                     . "$name is scalar and have no default value";

                throw new DefinitionException($msg);
            }
        }
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return array
     */
    protected function getParameterContext(\ReflectionParameter $parameter)
    {
        $context = array();

        if ($class = $parameter->getClass()) {
            $context['type'] = $class->getName();
        }

        if ($parameter->isOptional()) {
            $context['value'] = $parameter->getDefaultValue();
        }

        return $context;
    }

    /**
     * @param \ReflectionClass $reflection
     */
    protected function setMutators(\ReflectionClass $reflection)
    {
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $context = $this->getMutatorContext($method);

            if ($context) {
                $this->mutators[] = new Mutator($context);
            }
        }
    }

    /**
     * @param \ReflectionMethod $method
     * @return array|null
     * @throws DefinitionException
     */
    protected function getMutatorContext(\ReflectionMethod $method)
    {
        $context = array();

        $name = $method->getName();
        $parameters = $method->getParameters();

        if (!strncasecmp($name, 'set', 3) &&
            count($parameters) === 1 &&
            $class = $parameters[0]->getClass()
        ) {
            $context['name'] = $name;
            $context['type'] = $class->getName();
        }

        return $context;
    }
}