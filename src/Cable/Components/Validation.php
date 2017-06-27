<?php

namespace Cable\Validation;

class Validation
{
    /**
     * @var array
     */
    protected $rules = [];
    /**
     * @var array
     */
    protected $filters = [];
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $datas;
    /**
     * @var array
     */
    protected static $messages = [
        'required' => '$0 alanı doldurulması zorunludur.',
        'min' => '$0 alanına girilebilecek en küçük değer $1',
        'max' => '$0 alanına girilebilecek en büyük değer $1',
        'between' => '$0 alanına girilebilecek değerler $1 - $2 aralığında olmadılıdır.',
        'digit_min' => '$0 alanına en düşük $1 karekterli bir yazı girebilirsiniz.',
        'digit_max' => '$0 alanına en büyük $1 karekterli bir yazı girebilirsiniz.',
        'digit_betweem' => '$0 alanına $1 - $2 karekterli bir yazı girebilirsiniz.',
        'alpha' => '$0 alanına girilen değer bir a-zA-Z formatına uygun olmalıdır.',
        'numeric' => '$0 alanına girilen değer bir sayı olmalıdır.',
        'ip' => '$0 alanınına girilen değer bir ip adresine ait olmalıdır.',
        'url' => '$0 alanına girilen değer bir url adresine ait olmalıdır.',
        'email' => '$0 alanına girilen değer bir email adresine ait olmalıdır.',
        'alpha_numeric' => '$0 alanına girilen değer a-zA-Z0-9 formatına uygun olmalıdır.',
        'match_with' => '$0 alanına girilen değer $1 alanıyla aynı olmalıdır',
        'same_digit' => '$0 alanına girilen karekter uzunluğu $1 alanıyla eşit olmalıdır',

        'default' => '%s is not valid',
    ];

    /**
     * @var array
     */
    private static $rulesCallback = [
        'max' => 'handleRuleMax',
        'min' => 'handleRuleMin',
        'email' => 'handleRuleEmail',
        'url' => 'handleRuleUrl',
        'alpha' => 'handleRuleAlpha',
        'numeric' => 'handleRuleNumeric',
        'alpha_numeric' => 'handleRunAlphaNumeric',
        'digit_min' => 'handleRuleDigitMin',
        'digit_max' => 'handleRuleDigitMax',
        'digit_between' => 'handleRuleDigitBetween',
        'between' => 'handleRuleBetween',
        'ip' => 'handleRuleIp',
        'match_with' => 'handleRuleMatchWith',
        'required' => 'handleRuleRequired',
        'same_digit' => 'handleRuleSameDigit'
    ];

    /**
     * @param $name
     * @param callable $callback
     * @param $message
     */
    public static function addRule($name,callable $callback, $message){
        static::$rulesCallback[$name] = $callback;
        static::$messages[$name] = $message;
    }

    /**
     * @param $name
     * @param callable $callback
     */
    public static function addFilters($name, callable $callback){
        static::$filterCallbacks[$name] = $callback;
    }

    /**
     * @var array
     */
    private static $filterCallbacks = [
        'xss' => 'handleFilterXss',
    ];


    /**
     * Validation constructor.
     * @param array $datas
     * @param array $rules
     * @param array $filters
     */
    public function __construct(array $datas = [], array $rules = [], array $filters = [])
    {
        $this->setDatas($datas);

        if ( ! empty($filters)) {
            $this->setFilters($filters);
        }

        if ( ! empty($rules)) {
            $this->setRules($rules);
        }
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
        $validation =  new static($datas, $rules, $filters);


        return $validation->run($strict);

    }

    /**
     * @param $strict
     * @return $this
     */
    public function run($strict)
    {
        $this->handleFilters();
        $this->handleRules($strict);


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
     * @param $input
     * @param int $safe_level
     * @return mixed|string
     */
    private function cleanInput($input, $safe_level = 0)
    {
        $output = $input;
        do {
            // Treat $input as buffer on each loop, faster than new var
            $input = $output;
            // Remove unwanted tags
            $output = $this->stripTags($input);
            $output = $this->stripEncodedEntities($output);
            // Use 2nd input param if not empty or '0'
            if ($safe_level !== 0) {
                $output = $this->stripBase64($output);
            }
        } while ($output !== $input);

        return $output;
    }


    private function handleFilters()
    {
        $filters = $this->getFilters();
        $datas = &$this->datas;

        foreach ($filters as $data => $filter) {
            if ( ! isset($datas[$data])) {
                continue;
            }

            $datas[$data] = $this->resolveFilterbag($data, $filter);
        }

    }

    /**
     * @param bool $strict
     */
    private function handleRules($strict = true)
    {
        $rules = $this->getRules();
        $datas = $this->datas;

        foreach ($rules as $data => $rule) {
            $this->resolveRulebag($data, $rule, $strict);
        }
    }

    /**
     * @param $data
     * @param $rules
     * @param bool $strict
     */
    private function resolveRulebag($data, $rules, $strict = false)
    {
        $rules = $rules->getRules();

        foreach ($rules as $rule) {

            /**
             * @var Rule $rule
             */
            list($callback, $parameters) = $this->prepareCallbackAndParameters($data, $rule);

            $result = $callback(...$parameters);

            if ($result === false) {
                $this->errors = $this->prepareErrorMessage(
                    $data,
                    $rule->getError(),
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
     * @param $data
     * @param $rule
     * @return array
     */
    private function prepareCallbackAndParameters($data, $rule)
    {
        $callback = $rule->getCallback();
        $parameters = $rule->getParameters();


        if (is_string($callback)) {
            $callback = array($this, $callback);
        }

        $parameters = array(
            $data,
            $parameters,
            $this->datas,
        );

        return array($callback, $parameters);
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

            $callback = $filter->getCallback();

            if (is_string($callback)) {
                $callback = array($this, $callback);
            }

            $data = $callback($data);
        }

        return $data;
    }


    /*
     * Focuses on stripping encoded entities
     * *** This appears to be why people use this sample code. Unclear how well Kses does this ***
     *
     * @param   string  $input  Content to be cleaned. It MAY be modified in output
     * @return  string  $input  Modified $input string
     */
    private function stripEncodedEntities($input)
    {
        $input = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $input);
        $input = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $input);
        $input = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $input);
        $input = html_entity_decode($input, ENT_COMPAT, 'UTF-8');
        $input = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+[>\b]?#iu', '$1>', $input);
        $input = preg_replace(
            '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2nojavascript...',
            $input
        );
        $input = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2novbscript...',
            $input
        );
        $input = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u',
            '$1=$2nomozbinding...',
            $input
        );
        $input = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i',
            '$1>',
            $input
        );
        $input = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i',
            '$1>',
            $input
        );
        $input = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu',
            '$1>',
            $input
        );

        return $input;
    }

    /*
     * Focuses on stripping unencoded HTML tags & namespaces
     *
     * @param   string  $input  Content to be cleaned. It MAY be modified in output
     * @return  string  $input  Modified $input string
     */
    private function stripTags($input)
    {
        $input = preg_replace(
            '#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i',
            '',
            $input
        );
        $input = preg_replace('#</*\w+:\w[^>]*+>#i', '', $input);

        return $input;
    }

    /*
     * Focuses on stripping entities from Base64 encoded strings
     *
     * NOT ENABLED by default!
     * To enable 2nd param of clean_input() can be set to anything other than 0 or '0':
     * ie: xssClean->clean_input( $input_string, 1 )
     *
     * @param   string  $input      Maybe Base64 encoded string
     * @return  string  $output     Modified & re-encoded $input string
     */
    private function stripBase64($input)
    {
        $decoded = base64_decode($input);
        $decoded = $this->stripTags($decoded);
        $decoded = $this->stripEncodedEntities($decoded);
        $output = base64_encode($decoded);

        return $output;
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
     * @param $filter
     * @return Filterbag
     * @throws ValidationException
     */
    private function prepareFilter($filter)
    {
        $filters = explode('|', $filter);

        $filterBag = new Filterbag();
        foreach ($filters as $item) {
            if ( ! isset(static::$filterCallbacks[$item])) {
                throw new ValidationException(
                    sprintf(
                        '%s filter does not exists',
                        $item
                    )
                );
            }

            $callback = static::$filterCallbacks[$item];


            $filterBag->add(new Filter($item, $callback));
        }

        return $filterBag;
    }

    /**
     * @param $rule
     * @return Rulebag
     */
    private function prepareRules($rule)
    {
        $exploded = explode('|', $rule);

        $ruleBag = new Rulebag();
        foreach ($exploded as $item) {

            list($name, $parameters) = $this->prepareRuleParameters($item);
            list($callback, $message) = $this->prepareRuleCallbackMessage($name);

            $ruleBag->add(
                new Rule($name, $parameters, $message, $callback)
            );
        }

        return $ruleBag;
    }

    /**
     * @param string $name
     * @return array
     * @throws ValidationException
     */
    private function prepareRuleCallbackMessage($name)
    {
        if ( ! isset(static::$rulesCallback[$name])) {
            throw new ValidationException(
                sprintf('%s rule not found', $name)
            );
        }

        $callback = static::$rulesCallback[$name];

        $message = $name;

        if ( ! isset(static::$messages[$name])) {
            $message = 'default';
        }

        return array($callback, static::$messages[$message]);
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
     * @return bool
     */
    protected function handleRuleRequired($index)
    {
        return ( ! empty($this->datas[$index]));
    }


    /**
     * @param $index
     * @param array $params
     * @return bool
     */
    protected function handleRuleMin($index, $params = [])
    {
        $min = $params[0];
        $data = $this->datas[$index];

        return ($data >= $min);
    }

    /**
     * @param $index
     * @param array $params
     * @return bool
     */
    protected function handleRuleDigitMin($index, $params = [])
    {
        $min = isset($params[0]) ? $params[0] : false;
        if (false === $min) {
            return false;
        }
        $data = $this->datas[$index];

        return (strlen($data) >= $min);
    }

    /**
     * @param $index
     * @param array $params
     * @return bool
     */
    protected function handleRuleMax($index, $params = [])
    {
        $max = $params[0];
        $data = $this->datas[$index];

        return ($data < $max);
    }

    /**
     * @param $index
     * @param array $params
     * @return bool
     */
    protected function handleRuleDigitMax($index, $params = [])
    {
        $max = $params[0];
        $data = $this->datas[$index];

        return (strlen($data) < $max);
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
     * @return mixed
     */
    public function handleRuleEmail($index)
    {
        return filter_var($this->datas[$index], FILTER_VALIDATE_EMAIL);
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
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     * @return Model
     */
    public function setFilters($filters)
    {
        foreach ($filters as $filter) {
            $this->filters[] = $this->prepareFilter($filter);
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
     * @return Model
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

    /**
     * @return array
     */
    public function getDatas()
    {
        return $this->datas;
    }

    /**
     * @param array $datas
     * @return Validation
     */
    public function setDatas($datas)
    {
        $this->datas = $datas;

        return $this;
    }
}