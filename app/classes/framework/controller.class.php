<?php

/**
 * Intermediator between the application logic and the integration
 *
 * @author unreal4u
 */
class controller {
    /**
     * The pagetitle to be displayed
     * @var string
     */
    protected $pageTitle = '';

    /**
     * The view class which has to do with everything related to the view
     * @var unknown
     */
    public $view = null;

    /**
     * Links the basic classes to the controller so that they can be used
     */
    public function linkBasicClasses(appContainer $app=null) {
        $this->view = new view();
        $this->bc = $app->bc;
        $this->msgStack = $app->msgStack;
        $this->db = $app->db;
        $this->cache = $app->cache;
        $this->he = $app->he;
        $this->css = $app->css;
    }

    /**
     * Shorthand to assign things to template
     *
     * @param string $variableName
     * @param mixed $variableValue
     */
    public function assign($variableName, $variableValue=null) {
        $this->view->assign($variableName, $variableValue);
    }

    /**
     * Is able to prefetch an ajax action
     *
     * Important: when prefetching ajax content, this function will ignore any header() actions!
     *
     * @param string $controller
     * @param string $action
     * @param array $arguments
     * @return string
     */
    protected function ajaxPrefetch($controller, $action, array $arguments = array()) {
        $controllerName = 'controller_'.$controller;
        $actionName = 'action_'.ucwords($action);

        $ajaxController = new $controllerName();
        ob_start();
        $ajaxController->$actionName();
        $output = ob_get_clean();
        $this->assign('ajaxPrefetch_'.$controller.'_'.$action, $output);

        return $output;
    }

    /**
     * Creates an URL from the current controller/action
     */
    private function createUrlFromController() {
        return '';
    }
}
