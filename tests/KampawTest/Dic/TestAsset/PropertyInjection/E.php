<?php

namespace KampawTest\Dic\TestAsset\PropertyInjection;

class E implements NothingAwareInterface
{
    public $nothing;

    public function setNothing()
    {
        $this->nothing = true;
    }
}