<?php

class smartyWrapper extends Smarty {
    public function __construct() {
        parent::__construct();

        $this->setTemplateDir(ABSPATH . 'cache/smarty/templates');
        $this->setCompileDir(ABSPATH . 'cache/smarty/templates_c');
        $this->setConfigDir(ABSPATH . 'cache/smarty/configs');
        $this->setCacheDir(ABSPATH . 'cache/smarty/');
    }
}