<?php

namespace Kampaw\Dic;

interface DicAwareInterface
{
    /**
     * @return DicInterface
     */
    public function getDic();

    /**
     * @param DicInterface $dic
     * @return null
     */
    public function setDic(DicInterface $dic);
}