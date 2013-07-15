<?php

namespace configuration;

class defaultConfiguration extends \configuration\baseConfiguration {
    public $options = array();

    public function setOptions() {
        $this->options['APP_ENVIRONMENT'] = 'dev-u4u';
    }
}