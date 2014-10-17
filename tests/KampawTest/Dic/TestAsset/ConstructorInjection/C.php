<?php

namespace KampawTest\Dic\TestAsset\ConstructorInjection;

class C
{
    public $extra;

    public function __construct($extra = 'defaultValue')
    {
        $this->extra = $extra;
    }
}