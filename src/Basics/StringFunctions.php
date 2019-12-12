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
    public static function limit($input, $hardLimit = 40, $maxFlexibility = 10, $ellipsis = '…'): string
    {
        $parts = preg_split('/([\s\n\r]+)/u', $input, null, PREG_SPLIT_DELIM_CAPTURE);

        $stringLength = 0;
        $output = '';
        for ($iPart = 0; $iPart < count($parts); ++$iPart) {
            $stringLength += mb_strlen($parts[$iPart]);
            if ($stringLength > $hardLimit) {
                if (mb_strlen($output) < ($hardLimit - $maxFlexibility)) {
                    $output .= $parts[$iPart];
                    $output = substr($output, 0, $hardLimit);
                }
                break;
            }
            $output .= $parts[$iPart];
        }

        $suffix = '';
        if (mb_strlen($output) < mb_strlen($input)) {
            $output = substr($output, 0, $hardLimit - mb_strlen($ellipsis));
            $suffix = $ellipsis;
        }

        return $output.$suffix;
    }
 
    /**
     * @param string $length
     * @return string
     **/
    public static function generateString(int $length = 20): string
    {
        $finalString = "";
        $range = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $rangeMax = strlen($range) - 1;
        for ($i = 0; $i < $length; $i++) {
            $finalString .= $range[rand(0, $rangeMax)];
        }

        return $finalString;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function slugify(string $text): string
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
