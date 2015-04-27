<?php

namespace Kampaw\Dic\Assets\ConstructorInjection;

class ConcreteAndScalarParameter
{
    public function __construct(\stdClass $concrete, $scalar)
    {

    }
}