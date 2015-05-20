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
            throw new DefinitionException("Class $class is not instantiable");
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
        $this->concrete = '\\' . $reflection->getName();
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
        }
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return array
     * @throws DefinitionException
     */
    protected function getParameterContext(\ReflectionParameter $parameter)
    {
        $context = array();

        if ($class = $parameter->getClass()) {
            $context['type'] = '\\' . $class->getName();
        }
        elseif ($parameter->isOptional()) {
            $context['value'] = $parameter->getDefaultValue();
        }
        else {
            // @todo(kampaw) better message
            $name = $parameter->getName();
            $msg = "Cannot create runtime definition, parameter $name is scalar";

            throw new DefinitionException($msg);
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

        if (!strncasecmp($name, 'set', 3)) {
            $context['name'] = $name;
        }
        else {
            /* method name without a prefix, discard */
            return;
        }

        $parameters = $method->getParameters();

        if (empty($parameters)) {
            /* method with no parameters, discard */
            return;
        }
        elseif ($class = $parameters[0]->getClass()) {
            $context['type'] = '\\' . $class->getName();
        }
        else {
            /* method takes a scalar parameter, discard */
            return;
        }

        return $context;
    }
}