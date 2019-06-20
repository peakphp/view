<?php

declare(strict_types=1);

namespace Peak\View\Presentation;

class SinglePresentation extends Presentation
{
    /**
     * SingleLayoutPresentation constructor.
     * @param string $script
     * @param string|null $basePath
     */
    public function __construct(string $script, string $basePath = null)
    {
        parent::__construct([$script], $basePath);
    }
}
