<?php

namespace Kampaw\Dic\Assets\ConstructorInjection;

class ConcreteParameterWithDefaultValue
{
    public function __construct(\stdClass $concrete = null)
    {
        
    }
}