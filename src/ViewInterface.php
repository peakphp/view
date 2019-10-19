<?php

declare(strict_types=1);

namespace Peak\View;

use Peak\View\Presentation\PresentationInterface;

interface ViewInterface
{
    /**
     * @param string $var
     * @return mixed
     * @throws \Exception
     */
    public function &__get(string $var);

    /**
     * @param string $var
     * @return bool
     */
    public function __isset(string $var): bool;

    /**
     * @return string|false
     */
    public function render();

    /**
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function getVar(string $path, $default = null);

    /**
     * @return array
     */
    public function getVars(): array;

    /**
     * @param array $vars
     * @return mixed
     */
    public function setVars(array $vars);

    /**
     * @param array $vars
     * @return mixed
     */
    public function addVars(array $vars);

    /**
     * @return PresentationInterface|null
     */
    public function getPresentation(): ?PresentationInterface;

    /**
     * @param PresentationInterface $presentation
     * @return mixed
     */
    public function setPresentation(PresentationInterface $presentation);
}
