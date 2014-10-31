<?php

namespace KampawTest\Dic\TestAsset\PropertyInjection;

use Kampaw\Dic\DicAwareInterface;
use Kampaw\Dic\DicInterface;

class B implements DicAwareInterface
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