<?php

namespace KampawTest\Dic\TestAsset\ConstructorInjection;

class G implements GInterface
{
    public function __construct(GInterface $g)
    {
    }
}