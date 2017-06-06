<?php

namespace Cable\Validation\Facade;


use Cable\Facade\Facade;

class Validation extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'validation';
    }
}