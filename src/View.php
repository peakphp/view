<?php

declare(strict_types=1);

namespace Peak\View;

use Peak\View\Directive\DirectiveInterface;
use Peak\View\Exception\RenderException;
use Peak\View\Exception\VarNotFoundException;
use Peak\View\Presentation\PresentationInterface;
use Peak\View\Presentation\SinglePresentation;
use \Closure;
use \RuntimeException;

use function array_key_exists;
use function call_user_func_array;
use function file_exists;
use function is_array;
use function ob_end_clean;
use function ob_start;
use function ob_get_clean;


class View implements ViewInterface
{
    /**
     * @var array
     */
    protected $vars = [];

    /**
     * @var array<string, callable>
     */
    protected $helpers = [];

    /**
     * @var array<string, closure>
     */
    private $macros = [];

    /**
     * @var array<DirectiveInterface>
     */
    private $directives = [];

    /**
     * @var string|false
     */
    protected $layoutContent;

    /**
     * @var PresentationInterface
     */
    protected $presentation;

    /**
     * @var int
     */
    protected $obN = 0;

    /**
     * View constructor.
     * @param array|null $vars
     * @param PresentationInterface|null $presentation
     */
    public function __construct(array $vars = null, PresentationInterface $presentation = null)
    {
        if (isset($vars)) {
            $this->vars = $vars;
        }
        if (isset($presentation)) {
            $this->presentation = $presentation;
        }
    }

    /**
     * @param string $var
     * @return mixed
     * @throws \Exception
     */
    public function &__get(string $var)
    {
        if (!array_key_exists($var, $this->vars)) {
            throw new VarNotFoundException($var);
        }

        return $this->vars[$var];
    }

    /**
     * @param string $var
     * @return bool
     */
    public function __isset(string $var): bool
    {
        return array_key_exists($var, $this->vars);
    }

    /**
     * @param string $path
     * @param null $default
     * @return array|mixed|null
     */
    public function getVar(string $path, $default = null)
    {
        $array = $this->vars;

        if (!empty($path)) {
            $keys = explode('.', $path);
            foreach ($keys as $key) {
                if (!array_key_exists($key, $array)) {
                    return $default;
                }
                $array = $array[$key];
            }
        }

        return $array;
    }

    /**
     * @return array
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * @param array $vars
     * @return mixed
     */
    public function setVars(array $vars)
    {
        $this->vars = $vars;
        return $this;
    }

    /**
     * @param array $vars
     * @return mixed
     */
    public function addVars(array $vars)
    {
        $this->vars = array_merge($this->vars, $vars);
        return $this;
    }

    /**
     * @return PresentationInterface
     */
    public function getPresentation(): ?PresentationInterface
    {
        return $this->presentation;
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
     * @param array $directives
     * @return $this
     */
    public function setDirectives(array $directives)
    {
        $this->directives = $directives;
        return $this;
    }

    /**
     * Call a macro or helper in that order
     *
     * @param string $method
     * @param array $args
     * @return mixed
     * @throw RuntimeException
     */
    public function __call(string $method, array $args)
    {
        if ($this->hasMacro($method)) {
            return $this->callMacro($method, $args);
        } elseif(isset($this->helpers[$method])) {
            return call_user_func_array($this->helpers[$method], $args);
        }

        throw new RuntimeException('No macro or helper found for "'.$method.'"');
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHelper(string $name): bool
    {
        return array_key_exists($name, $this->helpers);
    }

    /**
     * @param string $name
     * @param callable $helper
     * @return $this
     */
    public function setHelper(string $name, Callable $helper)
    {
        $this->helpers[$name] = $helper;
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
     * @param Closure $macroCallable
     * @return $this
     */
    public function setMacro(string $name, Closure $macroCallable)
    {
        $this->macros[$name] = Closure::bind($macroCallable, $this, get_class());
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasMacro(string $name)
    {
        return isset($this->macros[$name]);
    }

    /**
     * Call a macro
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function callMacro(string $name, array $args)
    {
        if (isset($this->macros[$name])) {
            return call_user_func_array($this->macros[$name], $args);
        }
        throw new RuntimeException('There is no macro with the given name "'.$name.'" to call');
    }

    /**
     * @return false|string
     * @throws RenderException
     */
    public function render()
    {
        if (!isset($this->presentation)) {
            throw new RenderException('View has no Presentation to render');
        }
        $this->obN++;
        ob_start();
        $this->recursiveRender($this->presentation->getSources());
        $this->obN--;
        return $this->compileDirectives(ob_get_clean());
    }

    /**
     * @param string $content
     * @return string|string[]|null
     */
    public function compileDirectives(string $content)
    {
        foreach ($this->directives as $directive) {
            $content = $directive->compile($this, $content);
        }
        return $content;
    }

    /**
     * @param string $filename
     * @param array $vars
     * @return false|string
     * @throws RenderException
     */
    public function renderOrphan(string $filename, array $vars = [])
    {
        $view = clone $this;
        $view->setVars($vars);
        $view->setPresentation(new SinglePresentation($filename));
        return $view->render();
    }

    /**
     * @param array $filenames
     * @param array $vars
     * @return string
     * @throws RenderException
     */
    public function renderOrphans(array $filenames, array $vars = [])
    {
        $content = [];
        foreach ($filenames as $filename) {
            $content[] = $this->renderOrphan($filename, $vars);
        }
        return implode('', $content);
    }

    /**
     * @param string $filename
     * @param array $vars
     * @return false|string
     * @throws RenderException
     */
    public function renderChild(string $filename, array $vars = [])
    {
        $view = clone $this;
        $view->addVars($vars);
        $view->setPresentation(new SinglePresentation( $filename));
        return $view->render();
    }

    /**
     * @param array $filenames
     * @param array $vars
     * @return string
     * @throws RenderException
     */
    public function renderChildren(array $filenames, array $vars = [])
    {
        $content = [];
        foreach ($filenames as $filename) {
            $content[] = $this->renderChild($filename, $vars);
        }
        return implode('', $content);
    }

    /**
     * @param array $templateSources
     * @throws RenderException
     */
    protected function recursiveRender(array $templateSources)
    {
        foreach ($templateSources as $layout => $source) {
            if (is_array($source)) {
                $this->obN++;
                ob_start();
                $this->recursiveRender($source);
                $this->obN--;
                $this->layoutContent = ob_get_clean();
                $this->renderFile($layout);
                continue;
            }

            $this->renderFile($source);
        }
    }

    /**
     * @param string $file
     * @throws RenderException
     */
    protected function renderFile(string $file)
    {
        if (!file_exists($file)) {
            // make sure we close all out
            while ($this->obN > 0) {
                ob_end_clean();
                $this->obN--;
            }
            throw new RenderException('View file '.$file.' not found');
        }
        include $file;
    }


}
