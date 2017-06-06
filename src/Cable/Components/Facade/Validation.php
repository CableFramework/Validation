<?php

namespace Cable\Validation\Facade;


use Cable\Facade\Facade;

/**
 * Class Validation
 * @package Cable\Validation\Facade
 */
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
