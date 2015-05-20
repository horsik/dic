<?php

namespace Kampaw\Dic\Assets\ConstructorInjection;

class MultipleConcreteParameters
{
    public function __construct(\stdClass $first, \stdClass $second, \stdClass $third)
    {
        
    }
}