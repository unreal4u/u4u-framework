<?php

abstract class cronjobs {
    /**
     * Must be filled in by the functions!
     * @var array
     */
    private $_execute = array();

    private $_currentDate = null;

    public function __construct() {
        $this->_currentDate = new DateTime();
    }

    public function registerCronjob($name='', $interval=3600) {

    }

    public function executeCronjobs() {
        if (!empty($this->_execute)) {
            foreach ($this->_execute as $cronjobName) {
                if ($this->_checkrun($cronjobName)) {
                    $this->$cronjobName();
                }
            }
        }
    }

    /**
     * Checks whether it is time to run a cronjob
     *
     * @param string $name
     * @return boolean Returns true if cronjob must be run, false otherwise
     */
    private function _checkRun($name='') {

    }
}