<?php

class controller_admin extends controller {
    public function action_Index() {
        $this->pageTitle = __('Administration Panel');
        $this->app->misc->c_title($app->view->pageTitle, __('Welcome to the administration panel'));
    }

    public function action_Passwd() {
        $this->bc->add($this->app->myHome, __('Change password'));
        $this->app->misc->c_title('Cambiar Contrase&ntilde;a', 'En esta p&aacute;gina puede cambiar su contrase&ntilde;a de ingreso al sistema');
    }
}