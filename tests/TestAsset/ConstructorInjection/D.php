<?php

namespace KampawTest\Dic\TestAsset\ConstructorInjection;

class D
{
    public $extra;

    public function __construct(\stdClass $extra)
    {
    }
}