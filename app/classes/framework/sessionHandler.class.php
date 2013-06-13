<?php

class sessionHandler {
    /**
     * Initializes the session
     *
     * @return sessionHandler
     */
    public function initializeSession() {
        session_cache_limiter('private');
        ini_set("session.gc_maxlifetime", SESION_EXPIRE);
        ini_set("session.entropy_file", "/dev/urandom");
        ini_set("session.entropy_length", "512");
        // Session cache expires is in minutes, also controls Expires: and Cache-Control: max-age headers
        session_cache_expire(CACHE_EXPIRE / 60);
        session_name(SESION_NAME);
        session_start();
        $this->sessionId = session_id();
        if (empty($_SESSION['timeout'])) {
            $this->db->query('INSERT INTO sist_sessions (id_session,ip,useragent) VALUES (?,INET_ATON(?),?)', $this->sessionId, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
            $_SESSION['timeout'] = time() + SESION_EXPIRE;
        }

        return $this;
    }

    /**
     * Deals with timeout in the session and updating logged in and so on
     *
     * @param appContainer $app
     * @return sessionHandler
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
            if (APP_ENVIRONMENT === 'dev') {
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