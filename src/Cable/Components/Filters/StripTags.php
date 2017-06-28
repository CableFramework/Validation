<?php
namespace Cable\Validation\Filters;


use Cable\Validation\Filter;

/**
 * Class StripTags
 * @package Cable\Validation\Filters
 */
class StripTags extends Filter
{
    /**
     * @var string
     */
    protected $name = 'strip_tag';

    /**
     * @param mixed $data
     * @return mixed
     */
    public function execute($data)
    {
         return strip_tags(htmlentities(htmlspecialchars($data)));
    }
}