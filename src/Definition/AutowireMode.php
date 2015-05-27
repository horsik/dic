<?php

namespace Kampaw\Dic\Definition;

class AutowireMode
{
    const DISABLED    = 0;
    const CONSTRUCTOR = 2;
    const MUTATORS    = 4;
    const AUTODETECT  = 6;
}