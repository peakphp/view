<?php

declare(strict_types=1);

namespace Peak\View;

use Peak\Blueprint\Common\ResourceResolver;
use Peak\View\Presentation\PresentationInterface;
use Closure;

class ViewBuilder
{
    /**
     * @var array|null
     */
    protected $vars = null;

    /**
     * @var PresentationInterface
     */
    protected $presentation;

    /**
     * @var array
     */
    protected $macros = [];

    /**
     * @var array
     */
    protected $helpers = [];

    /**
     * @var HelperResolver|null
     */
    protected $helperResolver;

    /**
     * @var string
     */
    protected $viewClass;

    /**
     * ViewBuilder constructor.
     * @param ResourceResolver|null $helperResolver
     */
    public function __construct(ResourceResolver $helperResolver = null)
    {
        $this->helperResolver = $helperResolver ?? new HelperResolver(null);
    }

    /**
     * @param PresentationInterface $presentation
     * @return $this
     */
    public function setPresentation(PresentationInterface $presentation)
    {
        $this->presentation = $presentation;
        return $this;
    }

    /**
     * @param array $macros
     * @return $this
     */
    public function setMacros(array $macros)
    {
        $this->macros = $macros;
        return $this;
    }

    /**
     * @param string $name
     * @param Closure $macro
     * @return $this
     */
    public function setMacro(string $name, Closure $macro)
    {
        $this->macros[$name] = $macro;
        return $this;
    }

    /**
     * @param array $helpers
     * @return $this
     */
    public function setHelpers(array $helpers)
    {
        $this->helpers = $helpers;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $helper
     * @return $this
     */
    public function setHelper(string $name, $helper)
    {
        $this->helpers[$name] = $helper;
        return $this;
    }

    /**
     * @param array|null $vars
     * @return $this
     */
    public function setVars(?array $vars)
    {
        $this->vars = $vars;
        return $this;
    }

    /**
     * @param array $vars
     * @return $this
     */
    public function addVars(array $vars)
    {
        if (!is_array($this->vars)) {
            $this->vars = [];
        }

        $this->vars = array_merge($this->vars, $vars);
        return $this;
    }

    /**
     * @param string $viewClass
     * @return $this
     */
    public function setViewClass(string $viewClass)
    {
        $this->viewClass = $viewClass;
        return $this;
    }

    /**
     * @return View
     * @throws Exception\InvalidHelperException
     * @throws Exception\InvalidHelperException
     */
    public function build(): View
    {
        if (!isset($this->presentation)) {
            throw new \Exception('A presentation is required in order to create a view');
        }

        $viewClass = $this->viewClass ?? View::class;

        $view = new $viewClass($this->vars, $this->presentation);

        foreach ($this->helpers as $helperName => $helper) {
            if (isset($this->helperResolver)) {
                $helper = $this->helperResolver->resolve($helper);
            }
            $this->helpers[$helperName] = $helper;
        }

        $view->setHelpers($this->helpers);

        foreach ($this->macros as $macroName => $macro) {
            $view->setMacro($macroName, $macro);
        }

        return $view;
    }

    /**
     * @return false|string
     * @throws Exception\InvalidHelperException
     * @throws Exception\RenderException
     */
    public function render()
    {
        return $this->build()->render();
    }
}