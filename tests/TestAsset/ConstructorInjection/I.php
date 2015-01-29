<?php

namespace KampawTest\Dic\TestAsset\ConstructorInjection;

class I implements IInterface
{
    public $test;

    public function __construct($test = null)
    {
        $this->test = $test;
    }
}