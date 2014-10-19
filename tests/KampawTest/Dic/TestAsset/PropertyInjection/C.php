<?php

namespace KampawTest\Dic\TestAsset\PropertyInjection;

use Kampaw\Dic\DicAwareInterface;
use Kampaw\Dic\DicInterface;

class C implements DicAwareInterface, XAwareInterface
{
    protected $dic;
    protected $x;

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

    /**
     * @return mixed
     */
    public function getX()
    {
        return $this->x;
    }

    public function setX(XInterface $x)
    {
        $this->x = $x;
    }
}