<?php

class controller_index extends controller {
    /**
     * Almost all pages on this controller are public, so set this to true
     * @var boolean Defaults to true
     */
    public $isPublicPage = true;

    public function action_Index() {
        $this->pageTitle = _('Main index');
        $this->assign('mySpecialVar', _('This is an assigned variable from PHP!'));

        //$sistProblemIdentifier = new models\sistProblemIdentifier();
        //$sistProblemIdentifier->addProblem('This is a problem!... '.mt_rand(0, 1000), 4);

        return true;
    }

    public function action_Install() {
        #$this->misc->c_title(_('System installation'), _('This page will execute a serie of steps in order to install the system'));

        return true;
    }

    public function action_Login() {
        $this->pageTitle = _('Login');
        $this->bc->add($this->createUrlFromController(), _('Login'));

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
        $sessionHandler = new u4uSessionHandler();
        $sessionHandler->destroySession();

        $this->msgStack->add(3, _('You have been successfully logged out. It is now save to leave the computer'));
        $this->misc->redir(HOME.'login/');

        return true;
    }

    public function action_NoPermission() {
        $this->pageTitle = _('403 Forbidden access');
        header('x', true, 403);
        #$this->misc->c_title(_('We are sorry, but you have no permission to see this page'), sprintf(_('Please, %s to choose the right option'), $this->he->c_href(HOME,_('go back to our index'))));
        $this->he->c_tag('p',_('It is also possible that this page is under development right now'));

        return true;
    }

    public function action_NotFound() {
        $this->pageTitle = _('404 Not found');
        header('x', true, 404);
        #$app->misc->logActivity($r['id_user'], '404', $_SERVER['REQUEST_URI']);
        #$this->misc->c_title(_('We are sorry, but what you are looking for isn\'t here'), sprintf(_('Please, %s to choose the right option'), $this->he->c_href(HOME,_('go back to our index'))));
        #$this->he->c_tag('p',_('It is also possible that this page is under development right now'));

        return true;
    }
}
