<?php

namespace configuration;

class cliConfiguration extends \configuration\baseConfiguration {
    public $options = array();

    protected function setOptions() {
        $this->options['MEMORY_LIMIT'] = '1024M';
    }
}