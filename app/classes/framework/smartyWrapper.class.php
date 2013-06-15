<?php

class smartyWrapper extends Smarty {
    public $pageTitle = '';

    public function __construct($sistOptions) {
        parent::__construct();

        $this->setTemplateDir(USER_SPACE . 'views/');
        $this->setCompileDir(ABSPATH . $sistOptions['smartyCompileDir']);
        $this->setPluginsDir(array(CLASSES.'framework/smarty/plugins/framework', CLASSES.'framework/smarty/plugins/thirdparty'));
        $this->setConfigDir(ABSPATH . $sistOptions['smartyConfigDir']);
        $this->setCacheDir(ABSPATH . $sistOptions['smartyCacheDir']);
        $this->cache_lifetime = CACHE_EXPIRE;

        if (APP_ENVIRONMENT == 'production') {
            $this->caching = 1;
        } else {
            $this->caching = 0;
        }
    }

    public function fetchTemplate($tplLocation='') {
        $output = '';
        try {
            $output = $this->fetch($tplLocation);
        } catch (SmartyException $e) {
            $this->assign('errorMsg', $e->getMessage());
            $output = $this->fetch('index/error.tpl');
        }

        return $output;
    }
}