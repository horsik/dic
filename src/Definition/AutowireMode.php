<?php

namespace Kampaw\Dic\Definition;

class AutowireMode
{
    const DISABLED    = 0;
    const CONSTRUCTOR = 1;
    const MUTATORS    = 2;
    const AUTODETECT  = 3;
}