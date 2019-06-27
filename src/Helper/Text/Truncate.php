<?php

declare(strict_types=1);

namespace Peak\View\Helper\Text;

use function min;
use function preg_replace;
use function round;
use function strlen;
use function substr;

class Truncate
{
    /**
     * Truncate a string to a certain length if necessary, optionally splitting in the
     * middle of a word, and appending the $etc string or inserting $etc into the middle.
     *
     * @param  string  $string
     * @param  integer $length
     * @param  string  $etc
     * @param  bool    $breakWords
     * @param  bool    $middle
     * @return mixed   return a string if success, or false if substr() fail
     */
    public function __invoke(
        string $string,
        int $length = 80,
        string $etc = '...',
        bool $breakWords = false,
        bool $middle = false
    ) {
        if ($length == 0) {
            return '';
        }
        if (strlen($string) > $length) {
            $length -= min($length, strlen($etc));
            if (!$breakWords && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
            }
            if (!$middle) {
                return substr($string, 0, $length) . $etc;
            } else {
                $halfLength = (int)round($length/2, 0);
                return substr($string, 0, $halfLength) . $etc . substr($string, -$halfLength);
            }
        }
        return $string;
    }
}
