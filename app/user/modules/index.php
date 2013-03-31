<?php

class controller_index extends controller {
    public function action_Index() {
        $this->isPublicPage = true;
        $this->pageTitle = __('Main index');

        return true;
    }

    public function action_Install() {
        $this->isPublicPage = false;
        $this->app->misc->c_title(__('System installation'), __('This page will execute a serie of steps in order to install the system'));

        return true;
    }

    public function action_Login() {
        $this->isPublicPage = true;
        $this->bc->add($app->myHome, __('Login'));

        return true;
    }

    public function action_Logout() {
        $this->isPublicPage = true;
        #$this->app->misc->logActivity($r['id_user'], 'lou', 'logout');
        session_regenerate_id(true);
        $_SESSION = array();
        $_SESSION['loggedIn'] = false;
        $this->app->msgStack->add(3, __('You have been successfully logged out. It is now save to leave the computer'));
        $this->app->misc->redir(HOME.'login/');

        return true;
    }

    public function action_NoPermission() {
        $this->isPublicPage = true;
        $this->pageTitle = __('403 Forbidden access');
        header('x', true, 403);
        $this->app->misc->c_title(__('We are sorry, but you have no permission to see this page'), sprintf(__('Please, %s to choose the right option'), $app->he->c_href(HOME,__('go back to our index'))));
        $this->app->he->c_tag('p',__('It is also possible that this page is under development right now'));

        return true;
    }

    public function action_NotFound() {
        $this->isPublicPage = true;
        $this->pageTitle = __('404 Not found');
        header('x', true, 404);
        #$app->misc->logActivity($r['id_user'], '404', $_SERVER['REQUEST_URI']);
        $this->app->misc->c_title(__('We are sorry, but what you are looking for isn\'t here'), sprintf(__('Please, %s to choose the right option'), $app->he->c_href(HOME,__('go back to our index'))));
        $this->app->he->c_tag('p',__('It is also possible that this page is under development right now'));

        return true;
    }
}
