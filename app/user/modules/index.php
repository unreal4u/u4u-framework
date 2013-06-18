<?php

class controller_index extends controller {
    /**
     * Almost all pages on this controller are public, so set this to true
     * @var boolean Defaults to true
     */
    public $isPublicPage = true;

    public function action_Index() {
        $this->pageTitle = __('Main index');
        $this->assign('mySpecialVar', 'This is an assigned variable from PHP!');

        $sistProblemIdentifier = new models\sistProblemIdentifier();
        $sistProblemIdentifier->addProblem('This is a problem!', 4);

        return true;
    }

    public function action_Install() {
        #$this->misc->c_title(__('System installation'), __('This page will execute a serie of steps in order to install the system'));

        return true;
    }

    public function action_Login() {
        $this->pageTitle = __('Login');
        $this->bc->add($this->createUrlFromController(), __('Login'));

        if (!empty($_POST['loginUsername']) && !empty($_POST['loginPassword'])) {
            $oUser = new models\user();
            $user = $oUser->tryLogin($_POST['loginUsername'], $_POST['loginPassword']);
            if ($user->id > 0) {
                // Logged in!
            } else {
                // Not login, add msg
            }
        }

        return true;
    }

    public function action_Logout() {
        // Logout can only if we are logged in
        $this->isPublicPage = false;
        #$this->app->misc->logActivity($r['id_user'], 'lou', 'logout');
        $sessionHandler = new sessionHandler();
        $sessionHandler->destroySession();

        $this->msgStack->add(3, __('You have been successfully logged out. It is now save to leave the computer'));
        $this->misc->redir(HOME.'login/');

        return true;
    }

    public function action_NoPermission() {
        $this->pageTitle = __('403 Forbidden access');
        header('x', true, 403);
        #$this->misc->c_title(__('We are sorry, but you have no permission to see this page'), sprintf(__('Please, %s to choose the right option'), $this->he->c_href(HOME,__('go back to our index'))));
        $this->he->c_tag('p',__('It is also possible that this page is under development right now'));

        return true;
    }

    public function action_NotFound() {
        $this->pageTitle = __('404 Not found');
        header('x', true, 404);
        #$app->misc->logActivity($r['id_user'], '404', $_SERVER['REQUEST_URI']);
        #$this->misc->c_title(__('We are sorry, but what you are looking for isn\'t here'), sprintf(__('Please, %s to choose the right option'), $this->he->c_href(HOME,__('go back to our index'))));
        #$this->he->c_tag('p',__('It is also possible that this page is under development right now'));

        return true;
    }
}
