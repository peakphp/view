<?php

namespace Peak\View\Helper;

use Peak\View\Presentation\Presentation;
use Peak\View\ViewInterface;

class RenderChild
{
    public function __invoke(ViewInterface $view, string $file)
    {
        return $view->setPresentation(new Presentation([$file]))->render();
    }
}