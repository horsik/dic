<?php

namespace Kampaw\Dic\Definition;

use Kampaw\Dic\Definition\Parameter\AbstractParameter;

interface DefinitionInterface
{
    /**
     * @return string
     */
    public function getClass();

    /**
     * @return AbstractParameter[]
     */
    public function getParameters();
}