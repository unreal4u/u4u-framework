<?php

/**
 * Intermediator between the application logic and the integration
 *
 * @author unreal4u
 */
class controller {
    /**
     * The pagetitle to be displayed. Is only temporary holder because $appContainer will take and print it
     * @var string
     */
    public $pageTitle = '';

    /**
     * Links the basic classes to the controller so that they can be used
     */
    public function linkBasicClasses(appContainer $app=null) {
        $this->tplManager = $app->tplManager;
        $this->bc         = $app->bc;
        $this->msgStack   = $app->msgStack;
        $this->db         = $app->db;
        $this->cache      = $app->cache;
        $this->he         = $app->he;
        $this->css        = $app->css;
    }

    /**
     * Shorthand to assign things to template
     *
     * @param string $variableName
     * @param mixed $variableValue
     */
    public function assign($variableName, $variableValue=null) {
        $this->tplManager->assign($variableName, $variableValue);
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
    protected function createUrlFromController($controller='', $action='') {
        if (empty($controller)) {
            $controller = get_called_class();
        }

        if (empty($action)) {
            $action = 'action_Login';
        }

        // Controller and action do have "controller_" and "action_" as prefix
        // @TODO

        return HOME.$controller.'/'.$action.'/';
    }
}
