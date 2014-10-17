<?php

namespace KampawTest\Dic\TestAsset\ConstructorInjection;

class H implements HInterface
{
    public function __construct(XInterface $x, YInterface $y)
    {
    }
}