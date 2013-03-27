<?php

class uriValidator {
    public $defaultIndex = HOMEPAGE;
    public $notFound = array('index/not-found/', 'index', 'action_NotFound');
    public $loadThis = '';

    public function __construct($uri='') {
        $this->loadThis = $this->validateUri($uri);
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
        foreach($uriParts AS $uriPart) {
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
    private function convertUriToMethodName($methodName='') {
        $return = str_replace(' ', '', ucwords(str_replace('-', ' ', $methodName)));
        return $return;
    }

    private function getCandidates(array $uriParts=array()) {
        $readableCandidates = array();
        $controllerName = '';

        foreach($uriParts AS $uriPart) {
            $controllerName .= $uriPart;
            // First part: check what possible candidate files we have
            if (is_readable(CONTROLLERS.$controllerName.'.php')) {
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
    public function validateUri($uri='') {
        $return = array();

        $uriParts = $this->formatCandidateUri($uri);
        if (!empty($uriParts)) {
            // Always include our main index file, comes in handy later on
            $controllerName = '';

            if (count($uriParts) == 1 OR $uriParts[0] == 'index') {
                include(CONTROLLERS.'index.php');
                $rc = new \ReflectionClass('controller_index');
                $rcMethods = $rc->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($rcMethods as $rcMethod) {
                    $methods[] = $rcMethod->getName();
                }

                $methodName = 'action_'.$this->convertUriToMethodName($uriParts[0]);
                if (in_array($methodName, $methods)) {
                    $return = array($uri, 'index', $methodName);
                } else {
                    $uriParts[] = 'index';
                }
                unset($rc, $rcMethod, $rcMethods, $methods, $methodName);
            }

            if (empty($return)) {
                $readableCandidates = $this->getCandidates($uriParts);
                debug($readableCandidates, true, 'File candidates: ');

                if (!empty($readableCandidates)) {
                    foreach ($readableCandidates AS $readableCandidate) {
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
}