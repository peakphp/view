<?php

declare(strict_types=1);

namespace Peak\View\Directive;

use Peak\View\ViewInterface;

class EchoDirective implements DirectiveInterface
{
    /**
     * Echo variables
     *
     * Syntax:
     *  for single var: {{ $var }} (php equivalent: echo $this->$var)
     *  for array var: {{ $var.subkey }} (php equivalent: echo $this->$var['subkey'])
     *
     * @param ViewInterface $view
     * @param string $content
     * @return string|string[]|null
     */
    public function compile(ViewInterface $view, string $content)
    {
        $pattern = '/(@)?{{\s*\$(.+?)\s*}}(\r?\n)?/s';

        $callback = function ($matches) use ($view) {
            // this will ignore interpolation when preceding with @ (ex: @{{ $name }} will output {{ $name }})
            if ($matches[1] === '@') {
                return substr($matches[0],1);
            }
            return $view->getVar($matches[2]);
        };

        return preg_replace_callback($pattern, $callback, $content);
    }
}
