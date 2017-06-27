<?php

namespace Cable\Validation;

class Validation
{
    /**
     * @var RuleRepository
     */
    private $ruleRepository;

    /**
     * @var FilterRepository
     */
    private $filterRepository;


    /**
     * @var array
     */
    protected $errors = [];


    /**
     * @var array
     */
    protected static $messages = [
        'between' => '$0 alanına girilebilecek değerler $1 - $2 aralığında olmadılıdır.',
        'digit_min' => '$0 alanına en düşük $1 karekterli bir yazı girebilirsiniz.',
        'digit_max' => '$0 alanına en büyük $1 karekterli bir yazı girebilirsiniz.',
        'digit_betweem' => '$0 alanına $1 - $2 karekterli bir yazı girebilirsiniz.',
        'alpha' => '$0 alanına girilen değer bir a-zA-Z formatına uygun olmalıdır.',
        'numeric' => '$0 alanına girilen değer bir sayı olmalıdır.',
        'ip' => '$0 alanınına girilen değer bir ip adresine ait olmalıdır.',
        'alpha_numeric' => '$0 alanına girilen değer a-zA-Z0-9 formatına uygun olmalıdır.',
        'same_digit' => '$0 alanına girilen karekter uzunluğu $1 alanıyla eşit olmalıdır',
        'default' => '%s is not valid',
    ];



    /**
     * Validation constructor.
     * @param RuleRepository $ruleRepository
     * @param FilterRepository $filterRepository
     */
    public function __construct(RuleRepository $ruleRepository, FilterRepository $filterRepository)
    {
        $this->ruleRepository = $ruleRepository;
        $this->filterRepository = $filterRepository;
    }

    /**
     * @param array $datas
     * @param array $rules
     * @param array $filters
     * @param bool $strict
     * @return static
     */
    public function make(array $datas = [], array $rules = [], array $filters = [], $strict = true)
    {
        return $this->run($datas, $rules, $filters, $strict);

    }


    /**
     * @param array $datas
     * @param array $rules
     * @param array $filters
     * @param bool $strict
     * @throws ValidationException
     * @return $this
     */
    public function run(array $datas, array $rules,array  $filters, $strict = true)
    {
        foreach ($rules as $key => $rule){
            $data = isset($datas[$key]) ? $datas[$key] : nulll;

            if (isset($filters[$key])) {
                $data = $this->resolveFilterbag($this->prepareFilter($filters[$key]));
            }
        }

        foreach ($datas as $key => $data){
            if (isset($filters[$key])) {
                $data = $this->resolveFilterbag($this->prepareFilter($data));
            }

            if (isset($rules[$key])) {
                $this->resolveRulebag($data, $this->prepareRules($rules[$key], $datas), $strict);
            }

        }

        #$this->handleRules($datas, $this->prepareRules($rules));


        return $this;
    }

    /**
     * @param $data
     * @return mixed|string
     */
    protected function handleFilterXss($data)
    {
        return $this->cleanInput($data, 0);
    }



    /**
     * @param $data
     * @param $rules
     * @param bool $strict
     */
    private function resolveRulebag($data, Rulebag $rules, $strict = false)
    {
        $rules = $rules->getRules();

        foreach ($rules as $rule) {

            /**
             * @var Rule $rule
             */

            $result = $rule->handle($data);

            if (!$result) {
                $this->errors = $this->prepareErrorMessage(
                    $data,
                    $rule->getErrorMessage(),
                    $rule->getParameters()
                );


                // if we are running in strict mode, then first failiure will stop the process
                if ($strict === true) {
                    return;
                }
            }

        }
    }



    /**
     * @param mixed $data
     * @param Filterbag $filters
     * @return mixed
     */
    private function resolveFilterbag($data, $filters)
    {
        $filters = $filters->getFilters();

        foreach ($filters as $filter) {
            /**
             * @var Filter $filter
             */

            $data = $filter->execute($data);
        }

        return $data;
    }



    /**
     * @param $data
     * @return string
     */
    protected function handleFilterStripTags($data)
    {
        return strip_tags(htmlentities(htmlspecialchars($data)));
    }


    /**
     * @param string $filter
     * @return Filterbag
     * @throws ValidationException
     */
    private function prepareFilter($filter)
    {
        $filters = explode('|', $filter);

        $filterBag = new Filterbag();
        foreach ($filters as $item) {

            if ( ! $this->filterRepository->has($item)) {
                throw new ValidationException(
                    sprintf(
                        '%s filter does not exists',
                        $item
                    )
                );
            }

            $this->filterRepository->get($item);

            $filterBag->add($filter);
        }

        return $filterBag;
    }

    /**
     * @param string $rule
     * @param array $datas
     * @throws ValidationException
     * @return Rulebag
     */
    private function prepareRules($rule, array $datas = [])
    {

        $exploded = explode('|', $rule);

        $ruleBag = new Rulebag();
        foreach ($exploded as $item) {

            list($name, $parameters) = $this->prepareRuleParameters($item);

            if ( !$this->ruleRepository->has($name)) {
                throw new ValidationException(
                    sprintf('%s rule not found', $name)
                );
            }

            $rule = $this->ruleRepository->get($name);


            $rule->setParameters(!empty($parameters) ? $parameters : [])
                ->setDatas($datas);

            $ruleBag->add($rule);
        }

        return $ruleBag;
    }

    /**
     * @param string $item
     * @return array
     */
    private function prepareRuleParameters($item)
    {
        if (false !== strpos($item, ':')) {
            return explode(':', $item);
        }

        return array($item, array());
    }

    /**
     * @param $field
     * @param $message
     * @param $args
     * @return mixed
     */
    private function prepareErrorMessage($field, $message, $args)
    {
        $args = array_merge([$field], $args);
        foreach ($args as $index => $arg) {
            $message = str_replace('$'.$index, $arg, $message);
        }

        return $message;
    }

    /**
     * @param $index
     * @param array $params
     * @return bool
     */
    protected function handleRuleDigitBetween($index, $params = [])
    {
        $min = $params[0];
        $max = $params[1];
        $data = $this->datas[$index];

        return (strlen($data) >= $min && strlen($data) < $max);
    }

    /**
     * @param $index
     * @param array $params
     * @return bool
     */
    protected function handleRuleBetween($index, $params = [])
    {
        $min = $params[0];
        $max = $params[1];
        $data = $this->datas[$index];

        return ($data >= $min && $data < $max);
    }

    /**
     * @param $index
     * @return bool
     */
    public function handleRuleNumeric($index)
    {
        return is_numeric($this->datas[$index]);
    }

    /**
     * @param $index
     * @return bool
     */
    public function handleRuleAlpha($index)
    {
        return (preg_match("#^[a-zA-ZÀ-ÿ]+$#", $this->datas[$index]) === 1);
    }

    /**
     * @param $index
     * @return mixed
     */
    public function handleRuleUrl($index)
    {
        return filter_var($this->datas[$index], FILTER_VALIDATE_URL);
    }


    /**
     * @param $index
     * @return bool
     */
    public function handleRuleAlphaNumeric($index)
    {
        return (preg_match("#^[a-zA-ZÀ-ÿ0-9]+$#", $this->datas[$index]) === 1);
    }

    /**
     * @param $index
     * @param $args
     * @return bool
     */
    public function handleRuleMatchWith($index, $args)
    {
        $target = $args[0];

        return ($this->datas[$index] === $this->datas[$target]);
    }

    /**
     * @param $index
     * @param $args
     * @return bool
     */
    public function handleRuleSameDigit($index, $args)
    {
        $target = $args[0];

        return (strlen($this->datas[$index]) === strlen($this->datas[$target]));
    }

    /**
     * @param $index
     * @return mixed
     */
    public function ip($index)
    {
        return filter_var($this->datas[$index], FILTER_VALIDATE_IP);
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param array $rules
     * @return Model
     */
    public function setRules($rules)
    {
        foreach ($rules as $data => $rule) {
            $this->rules[$data] = $this->prepareRules($rule);
        }

        return $this;
    }



    /**
     * @return array
     */
    public function getMessages()
    {
        return static::$messages;
    }

    /**
     * @param array $messages
     * @return Validation
     */
    public function setMessages($messages)
    {
        if (count($messages) !== 0) {
            static::$messages = array_merge(static::$messages, $messages);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function failed()
    {
        return ! empty($this->errors);
    }

    /**
     * @return array
     */
    public function failings()
    {
        return $this->errors;
    }
}