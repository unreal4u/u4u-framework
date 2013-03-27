<?php

class controller_index extends controller {
    public function action_Index() {
        $this->isPublicPage = true;
    }

    public function action_Install() {
        $this->isPublicPage = false;
    }

    public function action_Login() {
        $this->isPublicPage = true;
    }

    public function action_Logout() {
        $this->isPublicPage = true;
    }

    public function action_NoPermission() {
        $this->isPublicPage = true;
    }

    public function action_NotFound() {
        $this->isPublicPage = true;
    }
}
