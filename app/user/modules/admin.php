<?php

class controller_admin extends controller {
    public function action_Index() {

    }

    public function action_Passwd() {
        $this->bc->add($this->app->myHome, __('Change password'));
    }
}