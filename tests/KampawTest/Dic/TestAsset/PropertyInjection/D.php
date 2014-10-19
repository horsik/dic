<?php

namespace KampawTest\Dic\TestAsset\PropertyInjection;

class D implements YAwareInterface
{
    public function setY(YInterface $x, $i)
    {
    }
}