<?php

namespace KampawTest\Dic\TestAsset\PropertyInjection;

use Kampaw\Dic\DicInterface;

class A
{
    protected $dic;

    /**
     * @return DicInterface
     */
    public function getDic()
    {
        return $this->dic;
    }

    /**
     * @param DicInterface $dic
     * @return null
     */
    public function setDic(DicInterface $dic)
    {
        $this->dic = $dic;
    }
}