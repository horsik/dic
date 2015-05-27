<?php

namespace Kampaw\Dic\Assets\MutatorInjection;

class ConcreteParameter
{
    protected $concrete;

    public function setConcrete(\stdClass $concrete)
    {
        $this->concrete = $concrete;
    }

    public function getConcrete()
    {
        return $this->concrete;
    }
}