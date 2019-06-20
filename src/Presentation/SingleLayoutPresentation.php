<?php

declare(strict_types=1);

namespace Peak\View\Presentation;

class SingleLayoutPresentation extends Presentation
{
    /**
     * SingleLayoutPresentation constructor.
     * @param string $layout
     * @param string $script
     * @param string|null $basePath
     */
    public function __construct(string $layout, string $script, string $basePath = null)
    {
        parent::__construct([$layout => [$script]], $basePath);
    }
}
