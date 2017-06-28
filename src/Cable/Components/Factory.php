<?php
namespace Cable\Validation;


use Cable\Validation\Filters\StripTags;
use Cable\Validation\Filters\Trim;
use Cable\Validation\Filters\Xss;
use Cable\Validation\Rules\Alpha;
use Cable\Validation\Rules\AlphaNumeric;
use Cable\Validation\Rules\Between;
use Cable\Validation\Rules\DigitBetween;
use Cable\Validation\Rules\DigitMax;
use Cable\Validation\Rules\DigitMin;
use Cable\Validation\Rules\Email;
use Cable\Validation\Rules\MatchWith;
use Cable\Validation\Rules\Max;
use Cable\Validation\Rules\Min;
use Cable\Validation\Rules\Numeric;
use Cable\Validation\Rules\Required;
use Cable\Validation\Rules\SameDigit;
use Cable\Validation\Rules\Url;
use Cable\Validation\Rules\Ip;

class Factory
{

    /**
     * @var array
     */
    protected  static  $filters = [
        Xss::class,
        StripTags::class,
        Trim::class
    ];

    /**
     * default rules
     *
     * @var array
     */
    protected static $rules = [
        Alpha::class,
        AlphaNumeric::class,
        Between::class,
        DigitBetween::class,
        DigitMax::class,
        DigitMin::class,
        Email::class,
        Ip::class,
        MatchWith::class,
        Max::class,
        Min::class,
        Numeric::class,
        Required::class,
        SameDigit::class,
        Url::class
    ];

    /**
     * @return Validation
     */
    public static function create(){
        $ruleRepository = new RuleRepository([]);
        $filterRepository = new FilterRepository([]);

        foreach (static::$rules as $rule){
            $ruleRepository->addRule(new $rule);
        }

        foreach (static::$filters as $filter){
            $filterRepository->addFilter(new $filter);
        }

        return new Validation($ruleRepository, $filterRepository);
    }

}