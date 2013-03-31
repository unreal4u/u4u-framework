<?php

class uriHandler {
    public $defaultIndex = HOMEPAGE;
    public $notFound = array(
        'index/not-found/', 'index', 'action_NotFound'
    );
    public $loadThis = '';

    public function __construct($uri = '') {
        if (!empty($uri)) {
            $this->loadThis = $this->validateUri($uri);
        }
    }

    /**
     * Applies some basic cleaning
     *
     * @param string $uri
     * @return array
     */
    private function formatCandidateUri($uri) {
        $uriParts = explode('/', $uri);
        // filter out empty strings
        $i = 0;
        foreach ($uriParts as $uriPart) {
            if (empty($uriPart)) {
                unset($uriParts[$i]);
            }
            $i++;
        }
        return $uriParts;
    }

    /**
     * Converts an uri to the standarized method name used by this framework
     *
     * @param string $methodName
     * @return string
     */
    private function convertUriToMethodName($methodName = '') {
        $return = str_replace(' ', '', ucwords(str_replace('-', ' ', $methodName)));
        return $return;
    }

    private function getCandidates(array $uriParts = array()) {
        $readableCandidates = array();
        $controllerName = '';
        foreach ($uriParts as $uriPart) {
            $controllerName .= $uriPart;
            // First part: check what possible candidate files we have
            if (is_readable(CONTROLLERS . $controllerName . '.php')) {
                $readableCandidates[] = $controllerName;
            }
            $controllerName .= '/';
        }
        return $readableCandidates;
    }

    /**
     * Validates the URI and gives back what controller and action to load
     *
     * @param string $uri
     * @return array Returns array in the form array($originalString, $controller, $action, $arguments)
     */
    public function validateUri($uri = '') {
        $return = array();
        $uriParts = $this->formatCandidateUri($uri);
        if (!empty($uriParts)) {
            // Always include our main index file, comes in handy later on
            $controllerName = '';
            if (count($uriParts) == 1 or $uriParts[0] == 'index') {
                include (CONTROLLERS . 'index.php');
                $rc = new \ReflectionClass('controller_index');
                $rcMethods = $rc->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($rcMethods as $rcMethod) {
                    $methods[] = $rcMethod->getName();
                }
                $methodName = 'action_' . $this->convertUriToMethodName($uriParts[0]);
                if (in_array($methodName, $methods)) {
                    $return = array(
                        $uri, 'index', $methodName
                    );
                } else {
                    $uriParts[] = 'index';
                }
                unset($rc, $rcMethod, $rcMethods, $methods, $methodName);
            }
            if (empty($return)) {
                $readableCandidates = $this->getCandidates($uriParts);
                debug($readableCandidates, true, 'File candidates: ');
                if (!empty($readableCandidates)) {
                    foreach ($readableCandidates as $readableCandidate) {
                        //
                    }
                } else {
                    $return = $this->notFound;
                }
                #if (is_readable(CONTROLLERS.$controllerName.'.php')) {
                #    include(CONTROLLERS.$controllerName.'.php');
                #    $rc = new \ReflectionClass($controllerName);
                #    $rcMethods = $rc->getMethods(\ReflectionMethod::IS_PUBLIC);
                #    $methodName = $this->convertUriToMethodName($uriParts[0]);
                #    if (in_array('action_'.$methodName, $rcMethods)) {
                #        $return = array($uri, $controllerName, $methodName);
                #    } else {
                #        if (in_array('action_Index', $rcMethods)) {
                #            $return = array($uri, $controllerName, 'action_Index');
                #        } else {
                #            $return = $this->notFound;
                #        }
                #    }
                #}
            }
        } else {
            $return = $this->notFound;
        }
        #debug($uriParts);
        #debug($return);
        return $return;
    }

    /**
     * Creates slugs from a random string
     *
     * This is partially based on the work of Alix Axel, with some fixes to support a lot of test cases the best way
     * possible
     *
     * @link http://stackoverflow.com/questions/2103797/url-friendly-username-in-php/2103815#2103815
     * @param string $string
     * @param boolean $convertSlash
     * @return string
     */
    public function createSlugFromString($string = '', $convertSlash = true) {
        $return = '';
        if (is_string($string) || is_numeric($string)) {
            $string = str_ireplace('&amp;', '&', $string);
            $return = strtolower(trim(preg_replace('~[^0-9a-z/]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
            if ($convertSlash) {
                $return = trim(preg_replace(array(
                    '[/]', '/-+/'
                ), '-', $return), '-');
            } else {
                $tmpReturn = explode('/', $return);
                $return = '';
                foreach ($tmpReturn as $stringPart) {
                    $return .= trim($stringPart, '-') . '/';
                }
                $return = substr(preg_replace('/\/+/', '/', $return), 0, -1);
            }
        }
        return $return;
    }
}