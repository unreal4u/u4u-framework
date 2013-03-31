<?php

class controller extends appContainer {
    /**
     * The pagetitle to be displayed
     * @var string
     */
    protected $pageTitle = '';

    /**
     * Breadcrump object
     * @var object
     */
    protected $bc = null;

    public function __construct() {
        $this->bc = $this->app->bc;
    }

    public function execute($module) {
        // new reflectionClass and such
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
        $output = $ajaxController->$actionName();
        return ob_get_clean();
    }

    public function __destruct() {
        // Render the page
    }
}
