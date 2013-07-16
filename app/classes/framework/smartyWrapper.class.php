<?php

class smartyWrapper extends \Smarty {
    public $pageTitle = '';

    /**
     * Constructor, sets all options
     *
     * @param array $sistOptions
     */
    public function __construct($sistOptions) {
        parent::__construct();

        $this->setTemplateDir(USER_SPACE . 'views/');
        $this->setCompileDir(realpath(ABSPATH . $sistOptions['smartyCompileDir']));
        $this->setPluginsDir(array(
            CLASSES.'framework/smarty/plugins/framework',
            CLASSES.'framework/smarty/plugins/thirdparty',
        ));
        $this->setConfigDir(realpath(ABSPATH . $sistOptions['smartyConfigDir']));
        $this->setCacheDir(realpath(ABSPATH . $sistOptions['smartyCacheDir']));
        $this->cache_lifetime = CACHE_EXPIRE;

        $this->caching = 1;
        if (APP_ENVIRONMENT != 'production') {
            $this->caching = 0;
        }

        $this->muteExpectedErrors();
    }

    public function __destruct() {
        $this->unmuteExpectedErrors();
        parent::__destruct();
    }

    /**
     * Fetches a template. If error present, returns error template
     *
     * @param string $tplLocation
     * @return string
     */
    public function fetchTemplate($tplLocation='') {
        $output = '';
        try {
            $output = $this->fetch($tplLocation);
        } catch (\SmartyException $e) {
            $this->assign('errorMsg', $e->getMessage());
            $output = $this->fetch('index/error.tpl');
        }

        return $output;
    }
}