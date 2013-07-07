<?php

namespace configuration;

class baseConfiguration {
    protected $currentConfigSet='';
    private $baseConfig = array();
    private $finalConfiguration = array();

    public function __construct() {
        $this->loadBaseConfig();
        $this->loadConfigurationLoader();
    }

    public function __destruct() {
        $this->unloadConfigurationLoader();
    }

    private function loadConfigurationLoader() {
        // Load class only within configuration namespace
    }

    private function unloadConfigurationLoader() {
        // Unload the defined auto loader
    }

    public function loadConfigurationSet($configurationSetName='') {
        // Loads a specific configuration; check if file exists and merge configuration options
    }

    private function loadBaseConfig() {
        // Loads the base class options
    }
}