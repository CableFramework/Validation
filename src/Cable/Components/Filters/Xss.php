<?php

namespace Cable\Validation\Filters;


use Cable\Validation\Filter;

/**
 * Class Xss
 * @package Cable\Validation\Filters
 */
class Xss extends Filter
{

    /**
     * @param mixed $data
     * @return mixed
     */
    public function execute($data)
    {
        return $this->cleanInput($data);
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
}
