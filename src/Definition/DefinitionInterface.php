<?php

namespace Kampaw\Dic\Definition;

interface DefinitionInterface
{
    /**
     * @return string
     */
    public function getClass();

    /**
     * @return array[Parameter]
     */
    public function getParameters();
}