<?php
namespace Cable\Validation;

/**
 * Class RuleRepository
 * @package Cable\Validation
 */
class RuleRepository
{

    /**
     * @var Rule[]
     */
    private $rules;

    /**
     * RuleRepository constructor.
     * @param Rule[] $rules
     */
    public function __construct(array $rules)
    {
        if ( !empty($rules)) {
            foreach ($rules as $rule){
                $this->addRule($rule);
            }
        }

    }

    /**
     * @param Rule $rule
     * @return $this
     */
    public function addRule(Rule $rule){
        $this->rules[$rule->getName()] = $rule;

        return $this;
    }

    /**
     * @param string $name
     * @return Rule
     */
    public function get($name){
        return $this->rules[$name];
    }
    /**
     * @param string $name
     * @return bool
     */
    public function has($name){
        return isset($this->rules[$name]);
    }
    /**
     * @return Rule[]
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param Rule[] $rules
     * @return RuleRepository
     */
    public function setRules($rules)
    {
        $this->rules = $rules;

        return $this;
    }
}