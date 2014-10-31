<?php

namespace KampawTest\Dic\TestAsset\ConstructorInjection;

use Kampaw\Dic\DicInterface;

class A
{
    public $dic;

    public function __construct(DicInterface $dic)
    {
        $this->dic = $dic;
    }
}