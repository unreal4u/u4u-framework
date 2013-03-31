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
     * Constructor
     */
    public function __construct() {
        #$this->misc     = new misc($this->db, $this->he);
        #$this->msgStack = new messageStack();
        $this->view     = new view();
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
}
