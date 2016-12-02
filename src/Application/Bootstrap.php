<?php
namespace Peak\Application;

use Peak\Registry;

/**
 * Application Bootstrapper base
 *   
 * @author   Francois Lajoie
 * @version  $Id$
 */
class Bootstrap
{

    /**
     * init bootstrap
     */
    public function __construct()
    {
        $this->_configView();
        $this->_configRouter();
        $this->_boot();
    }

    /**
     * Call all bootstrap methods prefixed by "init"
     *
     * @param string $prefix
     */
    private function _boot($prefix = 'init')
    {
        $c_methods = get_class_methods(get_class($this));
        $l = strlen($prefix);
        if(!empty($c_methods)) {
            foreach($c_methods as $m) {            
                if(substr($m, 0, $l) === $prefix) $this->$m();
            }
        }
    }

    /**
     * Configure view from app config
     */
    protected function _configView()
    {
        if(!isset(Registry::o()->config->view) || 
            !Registry::isRegistered('view')) return;

        $view  = Registry::o()->view;
        $cview = Registry::o()->config->view;

        if(!empty($cview)) {
            foreach($cview as $k => $v) {

                if(is_array($v)) {
                    foreach($v as $p1 => $p2) $view->$k($p1,$p2);
                }
                else $view->$k($v);
            }
        }
    }

    /**
     * Configure custom routes from app config
     */
    protected function _configRouter()
    {
        if(!isset(Registry::o()->config->router['addregex']) || 
            !Registry::isRegistered('router')) return;

        $r      = Registry::o()->router;
        $routes = Registry::o()->config->router['addregex'];

        if(!empty($routes)) {
            foreach($routes as $i => $exp) {
                $parts = explode(' | ', $exp);
                if(count($parts) == 2) {
                    $r->addRegex(trim($parts[0]), trim($parts[1]));

                }
            }
        }
    }
}