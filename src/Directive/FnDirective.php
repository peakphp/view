<?php

declare(strict_types=1);

namespace Peak\View\Directive;

use Peak\View\ViewInterface;

class FnDirective implements DirectiveInterface
{
    /**
     * Compile view macros & helpers
     *
     * Syntax:
     *  without params: @baseUrl() (php equivalent: $this->baseUrl())
     *  without args: @baseUrl($name) (php equivalent: $this->baseUrl($this->name))
     *
     * About argument:
     *  string must be surrounded by single quotes or double quotes
     *  ex: @function(3.14, 'bob', $email, "Mr.")
     *
     * @param ViewInterface $view
     * @param string $content
     * @return string|string[]|null
     */
    public function compile(ViewInterface $view, string $content)
    {
        $pattern = '/(\@([a-zA-Z0-9_]+)\((.+?)?\))?/s';

        $callback = function ($matches) use ($view) {

            if (empty($matches[0])) {
                return null;
            }

            $fn = $matches[2];
            $args = [];

            if (isset($matches[3])) {
                $args = $this->parseParameters($view, $matches[3]);
            }

            // check if function is a native php function or view helper/macro/method
            if (($view->hasMacro($fn) || $view->hasHelper($fn) || method_exists($view, $fn))) {
                return call_user_func_array([$view, $fn], $args);
            } else {
                return call_user_func_array($fn, $args);

            }
        };

        return preg_replace_callback($pattern, $callback, $content);
    }

    /**
     * @param ViewInterface $view
     * @param string $argsString
     * @return array
     */
    protected function parseParameters(ViewInterface $view, string $argsString): array
    {
        $args = [];
        $pattern = '/"([^"\\\\]*(\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(\\\\.[^\'\\\\]*)*)\'|\$[a-zA-Z0-9_.]+|[0-9.]+|[0-9]+/';

        // 1. look for quote string (single and double quote) and take into account escaped quotes
        // 2. look for view variable ($var)
        // 3. look for number (int and float)
        preg_match_all($pattern, $argsString, $matches, PREG_SET_ORDER, 0);

        // cleanup matches and prepare arguments real values
        foreach ($matches as $arg) {
            if (count($arg) == 1) {
                $args[] = (substr($arg[0], 0,1) === '$')
                    ? $view->getVar(substr($arg[0], 1))
                    : $arg[0];
            } else {
                $i = 1;
                foreach ($arg as $argVariation) {
                    if (!empty($argVariation)) {
                        if ($i == 2) {
                            $args[] = $argVariation;
                            break;
                        }
                        ++$i;
                    }
                }
            }
        }

        return $args;
    }
}
