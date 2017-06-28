<?php

namespace Cable\Validation\Rules;

use Cable\Validation\Rule;

/**
 * Class SameDigit
 * @package Cable\Validation\Rules
 */
class SameDigit extends Rule
{

    /**
     * @var string
     */
    protected $name = 'same_digit';


    /**
     * @var string
     */
    protected $errorMessage = '$0 alanına girilen karekter uzunluğu $1 alanıyla eşit olmalıdır';

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function handle($data)
    {
        return strlen($data) === strlen($this->getDatas()[$this->getParameters()[0]]);
    }
}