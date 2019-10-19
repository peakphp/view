<?php

declare(strict_types=1);

namespace Peak\View\Directive;

use Peak\View\ViewInterface;

interface DirectiveInterface
{
    public function compile(ViewInterface $view, string $content);
}
