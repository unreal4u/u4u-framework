<?php

class uriHandler {
    /**
     * Quick way to know what files NOT to include again
     * @var array
     */
    private $includedControllers = array();

    /**
     * The default index
     * @var string
     */
    public $defaultIndex = HOMEPAGE;

    /**
     * The not found page
     * @var array
     */
    public $notFound = array(
        'request' => 'index/not-found/',
        'controller' => 'controller_index',
        'action' => 'action_NotFound',
    );

    /**
     * The definitive class to load
     * @var array
     */
    public $loadThis = array();

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

        $finalParts = array();
        if (!empty($uriParts)) {
            foreach($uriParts AS $uriPart) {
                $finalParts[] = $uriPart;
            }
        }

        return $finalParts;
    }

    /**
     * Converts an uri to the standarized method name used by this framework
     *
     * @param string $methodName
     * @return string
     */
    private function convertUriToMethodName($methodName = '') {
        $return = 'action_' . str_replace(' ', '', ucwords(str_replace('-', ' ', $methodName)));
        return $return;
    }

    /**
     * Creates the module formatted array to be used by the rest of the system
     *
     * @param string $request
     * @param string $controller
     * @param string $action
     * @return array
     */
    private function createModuleArray($request, $controller, $action) {
        $return['request'] = $request;
        $return['controller'] = 'controller_'.$controller;
        $return['action'] = $action;
        $return['view'] = $controller.'/'.str_replace('action_', '', strtolower($action)).'.tpl';
        $return['abspath'] = CONTROLLERS . $controller . '.php';

        return $return;
    }

    /**
     * Checks for possible readable candidates
     *
     * @param array $uriParts
     * @return array
     */
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
     * Analyzes the given controller in search for usable methods
     *
     * @param string $controller
     * @return array
     */
    private function getMethodsOfController($controller) {
        $methods = array();

        $this->includeController($controller);
        $rc = new \ReflectionClass('controller_'.$controller);
        $rcMethods = $rc->getMethods(\ReflectionMethod::IS_PUBLIC);
        if (!empty($rcMethods)) {
            foreach($rcMethods AS $rcMethod) {
                $method = $rcMethod->getName();
                if (strpos($method, 'action_') === 0) {
                    $methods[] = $method;
                }
            }
        }

        return $methods;
    }

    /**
     * Includes the necesary files
     *
     * @param string $controller
     * @return boolean
     */
    public function includeController($controller) {
        $controller = str_replace('controller_', '', $controller);

        if (!in_array($controller, $this->includedControllers)) {
            include(CONTROLLERS . $controller . '.php');
            $this->includedControllers[] = $controller;
        }

        return true;
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
            $numberOfParts = count($uriParts);

            $controllerName = '';
            if ($numberOfParts == 1 || $uriParts[0] == 'index') {
                $methods = $this->getMethodsOfController('index');
                if ($numberOfParts > 1) {
                    $methodName = $this->convertUriToMethodName($uriParts[1]);
                } else {
                    $methodName = $this->convertUriToMethodName($uriParts[0]);
                }

                if (in_array($methodName, $methods)) {
                    $return = $this->createModuleArray($uri, 'index', $methodName);
                }
                unset($rc, $rcMethod, $rcMethods, $methods, $methodName);
                $uriParts[1] = 'index';
            }

            if (empty($return)) {
                $readableCandidates = $this->getCandidates($uriParts);
                if (!empty($readableCandidates)) {
                    foreach ($readableCandidates as $readableCandidate) {
                        $methods = $this->getMethodsOfController($readableCandidate);
                        $methodName = $this->convertUriToMethodName($uriParts[1]);
                        if (in_array($methodName, $methods)) {
                            $return = $this->createModuleArray($uri, $readableCandidate, $methodName);
                        }
                    }
                }
            }
        }

        if (empty($return)) {
            $return = $this->createModuleArray($this->notFound['request'], $this->notFound['controller'], $this->notFound['action']);
        }
        return $return;
    }

    /**
     * Creates a slug from a string
     *
     * Although copied from another developer, this function has been rewritten to support leaving slashes intact. as an
     * optional function argument
     *
     * @author alix.axel@gmail.com - http://stackoverflow.com/questions/2103797/url-friendly-username-in-php/2103815#2103815
     * @author Camilo Sperberg
     *
     * @param string $string The string from which we want to create the slug from
     * @param boolean $convertSlash Whether slashes will be converted to hyphens. Defaults to true
     * @return string Returns the slug, ready to be used as an url, if string is not valid, will return an empty string
     */
    public function createSlug($string='', $convertSlash=true) {
        $return = '';
        // Only if we have a valid string and the same is not empty
        if ((is_string($string) || is_numeric($string)) && $string != '') {
            $string = str_ireplace('&amp;', '&', $string);

            $return = strtolower(trim(preg_replace(
                '~[^0-9a-z/]+~i',
                '-',
                html_entity_decode(preg_replace(
                    '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|th|tilde|uml);~i',
                    '$1',
                    htmlentities($string, ENT_QUOTES, 'UTF-8')),
                    ENT_QUOTES,
                    'UTF-8')),
                '-')
            );

            // Do the intensive labor only if we have a string left to do something
            if ($return != '') {
                if ($convertSlash) {
                    // If we want to convert slashes to hyphens, a straightforward replace will do the job
                    $return = trim(preg_replace(array('[/]', '/-+/'), '-', $return), '-');
                } else {
                    // Check whether the original string ends with a slash
                    $endsWithSlash = false;
                    // At this point, it is save to use PHP's main functions because all multibyte strings will already be stripped out
                    if (strrpos($return, '/') == strlen($return) - 1) {
                        $endsWithSlash = true;
                    }
                    // Tear apart the string and whipe out some not-needed chars
                    $tmpReturn = explode('/', $return);
                    $return = '';
                    foreach($tmpReturn AS $stringPart) {
                        $return .= trim($stringPart, '-').'/';
                    }

                    // Finally, replace all extra slashes, including the now new end slash
                    $return = substr(preg_replace('/\/+/', '/', $return), 0, -1);

                    // Restore the last slash but only if it was present
                    if ($endsWithSlash) {
                        $return .= '/';
                    }
                }
            }
        }

        return $return;
    }
}