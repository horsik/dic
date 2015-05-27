<?php

namespace Kampaw\Dic\Assets\ConstructorInjection;

class CircularConcreteParameter
{
    public function __construct(CircularConcreteParameter $concrete)
    {
        
    }
}