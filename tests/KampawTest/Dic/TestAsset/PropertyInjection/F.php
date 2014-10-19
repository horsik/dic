<?php

namespace KampawTest\Dic\TestAsset\PropertyInjection;

class F implements UppercaseNothingAwareInterface
{
    public $nothing;

    public function SETNOTHING()
    {
        $this->nothing = true;
    }
}