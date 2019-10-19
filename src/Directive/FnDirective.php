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
     * @param ViewInterface $view
     * @param string $content
     * @return string|string[]|null
     */
    public function compile(ViewInterface $view, string $content)
    {
        $pattern = '/(\@([a-zA-Z0-9_]+)\((.+?)?\))?/s';

        $callback = function ($matches) use ($view) {

            if (empty($matches[0])) {
                return;
            }

            $fn = $matches[2];
            $argsArray = [];
            $isNative = !($view->hasMacro($fn) || $view->hasHelper($fn) || method_exists($view, $fn));

            if (isset($matches[3])) {
                $args = $matches[3];
                $argsArray = explode(',', $args);
                foreach ($argsArray as $i => $arg) {
                    if (substr($arg, 0, 1) === '$') {
                        $varName = substr($arg, 1, strlen($arg));
                        $argsArray[$i] = $view->getVar($varName);
                    }
                }
            }

            if ($isNative) {
                return call_user_func_array($fn, $argsArray);
            } else {
                return call_user_func_array([$view, $fn], $argsArray);
            }
        };

        return preg_replace_callback($pattern, $callback, $content);
    }
}
