<?php

namespace KampawTest\Dic\TestAsset\ConstructorInjection;

class B
{
    public function __construct(\DateTimeInterface $time)
    {
    }
}