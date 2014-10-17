<?php

namespace KampawTest\Dic\TestAsset\ConstructorInjection;

class F implements FInterface
{
    public $x;

    public function __construct(XInterface $x)
    {
        $this->x = $x;
    }
}