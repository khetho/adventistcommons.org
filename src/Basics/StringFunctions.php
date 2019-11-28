<?php

namespace AdventistCommons\Basics;

class StringFunctions
{
    /**
     * Shorten a string to a char count limit.
     * Tries to find a graceful place to stop it (a space or a punctuation).
     * Add an ellipsis if shorten.
     *
     * @param $input
     * @param int $softLimit
     * @param int $maxFlexibility
     * @param string $ellipsis
     * @return string
     */
    public static function limit($input, $softLimit = 30, $maxFlexibility = 10, $ellipsis = '…')
    {
        $parts = preg_split('/([\s\n\r]+)/u', $input, null, PREG_SPLIT_DELIM_CAPTURE);
        $parts_count = count($parts);
        
        $length = 0;
        $last_part = 0;
        for (; $last_part < $parts_count; ++$last_part) {
            $length += strlen($parts[$last_part]);
            if ($length > $softLimit) {
                break;
            }
        }
        
        $output = implode(array_slice($parts, 0, $last_part));
        if (strlen($output) > $softLimit + $maxFlexibility) {
            $output = substr($output, 0, $softLimit + $maxFlexibility);
        }
        $suffix = '';
        if (strlen($output) < strlen($input)) {
            $suffix = $ellipsis;
        }
        
        return $output.$suffix;
    }
}
