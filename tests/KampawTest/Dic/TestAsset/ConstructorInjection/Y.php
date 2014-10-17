<?php

namespace KampawTest\Dic\TestAsset\ConstructorInjection;

class Y implements YInterface
{
    public function __construct(HInterface $h)
    {
    }
}