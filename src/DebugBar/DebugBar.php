<?php

namespace Peak\DebugBar;

use Peak\Common\Collection;
use Peak\Common\Interfaces\Renderable;
use Peak\Common\Session;
use Peak\DebugBar\View\Layout;


/**
 * Debug bar
 */
class DebugBar implements Renderable
{
    /**
     * Default Modules List
     * @var array
     */
    protected $modules = [
        \Peak\DebugBar\Modules\ExecutionTime\ExecutionTime::class,
        \Peak\DebugBar\Modules\Memory\Memory::class,
        \Peak\DebugBar\Modules\Message\Message::class,
        \Peak\DebugBar\Modules\Files\Files::class,
        \Peak\DebugBar\Modules\Session\Session::class,
        \Peak\DebugBar\Modules\Inputs\Inputs::class,
    ];

    /**
     * Modules object instances
     * @var array
     */
    protected $modules_instances = [];

    /**
     * Storage handler
     * @var AbstractStorage|SessionStorage
     */
    protected $storage;

    /**
     * DebugBar constructor
     */
    public function __construct(AbstractStorage $storage = null, array $modules = [])
    {
        if (!isset($storage)) {
            $storage = new SessionStorage();
        }
        $this->storage = $storage;

        if (!empty($modules)) {
            $this->setModules($modules);
        }
    }

    /**
     * Add module
     *
     * @param string $module
     * @return $this
     */
    public function addModule($module)
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * Add modules
     *
     * @param array $modules
     * @return $this
     */
    public function addModules(array $modules)
    {
        $this->modules = array_merge($this->modules, $modules);
        return $this;
    }

    /**
     * Overwrite modules
     *
     * @param array $modules
     * @return $this
     */
    public function setModules(array $modules)
    {
        $this->modules = $modules;
        return $this;
    }

    /**
     * Render debug bar modules
     *
     * @return null|string
     * @throws View\ViewNotFoundException
     */
    public function render()
    {
        $content = '';
        $tabs = [];

        foreach ($this->modules as $module) {
            $module_obj = new $module($this->storage);
            if ($module_obj->isRenderDisabled()) {
                continue;
            }

            $tab = $module_obj->renderTitle();
            $logo = $module_obj->renderLogo();
            if (!empty($tab) && !empty($logo)) {
                $tab = $logo.' '.$tab;
            }
            $tabs[$module_obj->getName()] = $tab;

            $content .= $this->renderModule($module_obj);
        }

        $layout_content = new Collection([
            'tabs' => $tabs
        ]);

        return (new Layout(
            __DIR__.'/View/scripts/bar.layout.php',
                $layout_content,
                $content)
            )->render();
    }

    /**
     * Render a module an throw also proper warning in cass module doesn't implement Renderable
     *
     * @param Renderable $module
     * @return mixed
     */
    protected function renderModule(Renderable $module)
    {
        return $module->render();
    }
}