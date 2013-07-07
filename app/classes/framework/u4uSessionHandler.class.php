<?php

/**
 * Handles everything related to sessions
 *
 * @author unreal4u
 */
class u4uSessionHandler {
    /**
     * Initializes the session
     *
     * @return sessionHandler
     */
    public function initializeSession(appContainer $app) {
        session_cache_limiter('private');
        ini_set("session.gc_maxlifetime", SESION_EXPIRE);
        ini_set("session.entropy_file", "/dev/urandom");
        ini_set("session.entropy_length", "512");
        // Session cache expires is in minutes, also controls Expires: and Cache-Control: max-age headers
        session_cache_expire(CACHE_EXPIRE / 60);
        session_name(SESSION_NAME);
        session_save_path(SESSION_PATH);
        session_start();
        $app->sessionId = session_id();
        if (empty($_SESSION['timeout'])) {
            $app->db->query('INSERT INTO sist_sessions (id_session,ip,useragent) VALUES (?,INET_ATON(?),?)', $app->sessionId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
            $_SESSION['timeout'] = time() + SESION_EXPIRE;
        }

        return $this;
    }

    /**
     * Destroys the session
     *
     * @param appContainer $app The appContainer
     * @return boolean Returns always true
     */
    public function destroySession(appContainer $app=null) {
        // @TODO
        //$logActivity = new models\logs();
        //$logActivity->log($_SESSION['idUser'], 'lou', 'logout');
        session_regenerate_id(true);
        $_SESSION = array();
        $_SESSION['loggedIn'] = false;

        return true;
    }

    /**
     * Destroys and then reinitializes the session
     *
     * @param appContainer $app
     * @return sessionHandler Returns itself
     */
    public function reinitializeSession(appContainer $app) {
        $this->destroySession();
        return $this->initializeSession($app);
    }

    /**
     * Deals with timeout in the session and updating logged in and so on
     *
     * @param appContainer $app
     * @return sessionHandler Returns itself
     */
    public function setTimeout(appContainer $app) {
        if (empty($app->options['installed']) && $app->module != 'install/index') {
            $app->misc->redir(HOME . 'install/');
        }
        if (empty($app->options['active_theme'])) {
            $app->options['active_theme'] = 'default';
        }

        if (empty($_SESSION['loggedIn'])) {
            $_SESSION['loggedIn'] = false;
        } else {
            if (APP_ENVIRONMENT != 'production') {
                $app->sessionExpireInformation = (time() + SESION_EXPIRE) - $_SESSION['timeout'];
                if ($app->sessionExpireInformation < 0) {
                    $app->sessionExpireInformation = abs($app->sessionExpireInformation) + SESION_EXPIRE;
                }
            }
            if (time() > $_SESSION['timeout']) {
                $_SESSION['loggedIn'] = false;
                $app->misc->redir(HOME . 'logout/');
            } else {
                $_SESSION['timeout'] = time() + SESION_EXPIRE;
            }
        }

        $app->loggedIn = $_SESSION['loggedIn'];

        return $this;
    }
}