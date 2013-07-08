<?php

use u4u\debugInfo;
/**
 * The main application container. This class holds the entire application
 *
 * @package General
 * @author Camilo Sperberg - http://unreal4u.com/
 * @license BSD License. Feel free to use and modify
 */
class appContainer {
    /**
     * Contains the current relative page (only if request is valid)
     * @var string
     */
    public $myHome = '';

    /**
     * The additional javascript files that are going to be parsed, add via @see $this->addJavascriptFile()
     * @var array
     */
    public $javascriptFiles = array();

    /**
     * Additional javascript code that is going to be included in the header
     * @var array
     */
    public $javascriptCode = array();

    /**
     * Holds the start of the current request
     * @var float
     */
    public $timeRequestBegin = 0;

    /**
     * Contains all options of the site
     * @var array
     */
    public $options = array();

    /**
     * Whether the user is logged in or not. Defaults to false
     * @var boolean
     */
    public $loggedIn = false;

    /**
     * Internal pointer to know whether the requested page has been found or not. Don't mess with this
     * @var boolean
     */
    public $found = false;

    /**
     * Which module to execute
     * @var array
     */
    public $executeModule = '';

    /**
     * Contains the left menu
     * @var array
     */
    public $menu = array();

    /**
     * Will be filled in by the controller, this class just takes it over
     * @var string
     */
    public $pageTitle = '';

    /**
     * The contents of the processed and requested page
     * @var string
     */
    public $pageContents = '';

    /**
     * Defaults to true if request is ajax based
     * @var boolean
     */
    public $isAjaxRequest = false;

    /**
     * Whether to load all header or not (eg: ajax requests). Defaults to true
     * @var boolean
     */
    public $loadHeaders = true;

    /**
     * Holds all the modules of the system
     * @var array
     */
    protected $modules = array();

    /**
     * What the current request should point to
     * @var string
     */
    public $request = '';

    /**
     * Additional parameters
     * @var string
     */
    public $additionalRequestParameters = '';

    /**
     * If the current request is a public page, this will be true. If the current request needs authentification, it will be false
     * @var boolean
     */
    public $isPublicPage = false;

    /**
     * Autoloader and instantiator of u4u classes
     * @var object
     */
    private $u4uAutoLoader = null;

    /**
     * Contains the session class
     * @var object
     */
    private $sessionHandler = null;

    /**
     * Constructor
     */
    public function __construct() {
        if (APP_ENVIRONMENT != 'production') {
            $this->timeRequestBegin = microtime(true);
        }
        chdir(ABSPATH);

        $this->locale = setlocale(LC_ALL, 'es_ES', 'es_CL', 'es', 'ES' );
        date_default_timezone_set('America/Santiago');

        spl_autoload_register(array(
            $this,
            'autoloadHandler'
        ));
    }

    /**
     * Destructor
     */
    public function __destruct() {
        if (APP_ENVIRONMENT != 'production') {
            $message = u4u\debugInfo::getDateStamp();
            $message .= $this->executeModule['controller'].'/'.$this->executeModule['action'];
            $message .= '; '.__('Time: '.(microtime(true) - $this->timeRequestBegin).' secs');
            debugFile($message, 'loadTimes.txt', ABSPATH.'cache/logs/');
        }
        $this->db = null;
        $this->he = null;
        $this->bc = null;
        $this->msgStack = null;
        $this->tplManager = null;
        $this->cache = null;
    }

    /**
     * Initialize the most basic classes and the session also
     */
    public function initializeMainObject() {
        $this->includeThirdparty(U4U_CLASSES);
        $this->u4uAutoLoader   = new \u4u\autoLoader();
        $this->db              = $this->u4uAutoLoader->instantiateClass('db_mysqli');
        if (APP_ENVIRONMENT != 'production') {
            $this->db->keepLiveLog = true;
        }

        $this->sessionHandler = new u4uSessionHandler();
        $this->sessionHandler->initializeSession($this);

        $this->registerBasicClasses();

        return $this;
    }

    /**
     * The basic classes with which this framework works
     */
    private function registerBasicClasses() {
        try {
            $this->cache    = $this->u4uAutoLoader->instantiateClass('cacheManager' , array('apc'));
            if (APP_ENVIRONMENT != 'production') {
                $this->cache->enableDebugMode();
            }
        } catch (Exception $e) {
            // @TODO fix when APC is not enabled, for the moment do nothing
        }
        $this->he       = $this->u4uAutoLoader->instantiateClass('HTMLUtils');
        $this->css      = $this->u4uAutoLoader->instantiateClass('csstacker');
        $this->u4uAutoLoader->unregisterAutoLoader();
        unset($this->u4uAutoLoader);

        $this->bc       = new breadcrump();
        $this->msgStack = new messageStack();

        return $this;
    }

    /**
     * The autoloader of the framework
     * @param string $class The class name we want to load
     * @return boolean Returns true if class could be included, false otherwise
     */
    private function autoloadHandler($class) {
        $return = true;
        // Loads the classes
        if (is_readable(CLASSES . $class . '.class.php')) {
            include (CLASSES . $class . '.class.php');
        } else {
            if (strpos($class, 'models\\') === 0) {
                $class = str_replace('\\', '/', $class);
            }

            // Loads the models
            if (is_readable(USER_SPACE.$class.'.php')) {
                include(USER_SPACE.$class.'.php');
            }
            $return = false;
        }

        return $return;
    }

    /**
     * Instantiates and configures Smarty for general use
     */
    protected function _setupView($whichView='smarty') {
        switch ($whichView) {
            case 'smarty':
                $this->includeThirdparty(TP_SMARTY);
                $this->tplManager = new smartyWrapper($this->options);
            break;
            default:
                throw new Exception('View not supported!');
            break;
        }

        return $this;
    }

    /**
     * Unloads the view class and associated stuff
     */
    protected function _unloadView() {
        $this->tplManager = null;
        return $this;
    }

    /**
     * Includes a third party class from the thirdparty directory
     * @param constant $thirdPartyId The constant name we are trying to include
     */
    public function includeThirdparty($thirdPartyId='') {
        include (THIRDPARTY_DIRECTORY . $thirdPartyId);

        return $this;
    }

    /**
     * Adds a javascript file to the stack in order to be included at the homepage
     *
     * @param string $filename The name (and/or route) to the javascript file to be included
     * @return boolean Returns always true
     */
    public function addJavascriptFile($filename='') {
        $this->javascriptFiles[] = $filename;
        return true;
    }

    /**
     * Checks, sets and validates the module/controller we are trying to load
     *
     * @return string The definite route of the file we want to load
     */
    public function validateRoute() {
        $return = '';

        $uriHandler = new uriHandler();
        // Check if we can retrieve the to-be-loaded module from cache
        $return = $this->cache->load('u4u-loadModule', array('u4u-internals', $_SERVER['REQUEST_URI']));
        if ($return === false) {
            if (!empty($_SERVER['REQUEST_URI'])) {
                $uri = str_replace(REWRITE_BASE, '', $_SERVER['REQUEST_URI']);
            }
            if (empty($uri)) {
                $uri = HOMEPAGE;
            }

            // Validate the URI and save it into cache (can be pretty intensive)
            $return = $uriHandler->validateUri($uri);
            $this->cache->save($return, 'u4u-loadModule', array('u4u-internals', $_SERVER['REQUEST_URI']), 3600);
        }

        // If everything was rescued from cache, the controller hasn't been included. Do so now
        $uriHandler->includeController($return['controller']);

        $this->executeModule = $return;
        return $return;
    }

    /**
     * Loads all options from the database or cache
     *
     * @return array Returns an array with all options
     */
    public function loadOptions() {
        $this->options = $this->cache->load('u4u-siteOptions', array('u4u-internals'));
        if (empty($this->options)) {
            $aOptions = $this->db->query('SELECT name AS option_name,v FROM sist_options WHERE id_option = ?', 'sop');
            if ($this->db->num_rows > 0) {
                foreach ($aOptions as $a) {
                    $this->options[$a['option_name']] = $a['v'];
                }
            }
            $this->cache->save($this->options, 'u4u-siteOptions', array('u4u-internals'), 3600);
        }

        $this->sessionHandler->setTimeout($this);
        return $this;
    }

    /**
     * Executes the selected module
     *
     * @param array $module
     */
    public function execute($whichView='smarty') {
        $this->_setupView($whichView);
        $controllerName = $this->executeModule['controller'];
        $methodName = $this->executeModule['action'];

        $controller = new $controllerName();
        $controller->linkBasicClasses($this);
        $controller->$methodName();
        $this->pageTitle = $controller->pageTitle;
        $this->pageContents = $controller->tplManager->fetchTemplate($this->executeModule['view']);
        $this->_unloadView();

        return $this;
    }

    /**
     * Sets the left menu and sets the "found" variable
     *
     * @return int Returns 1 if user has no menu or 0 otherwise
     */
    public function setMenuAndCheck() {
        $return = $i = 0;
        $groupString = '';
        foreach ($_SESSION['id_grp'] as $a) {
            if ($i != 0) {
                $groupString .= ' OR ';
            }
            $groupString .= 'id_grp = ' . $a;
            $i++;
        }

        $cacheResult = $this->cache->load('u4u-leftmenu', array('u4u-internals', 'groupString' => $groupString));
        if (empty($cacheResult)) {
            $this->menu = $this->db->query('SELECT link,description,id_grp FROM sist_menu WHERE (' . $groupString . ' OR id_grp = ?) AND visible = ? ORDER BY id_order', 1, 1);
            if ($this->db->num_rows > 0) {
                foreach ($this->menu as $a) {
                    if (!$this->found and $a['link'] == $this->request) {
                        $this->found = true;
                    }
                }
            } else {
                $return = 1;
            }

            // Save return, menu and found into one big array in cache, saves a lot of processing power on later requests
            $this->cache->save(
                array('return' => $return, 'menu' => $this->menu, 'found' => $this->found,),
                'u4u-leftmenu',
                array('u4u-internals', 'groupString' => $groupString),
                3600
            );
        } else {
           $return      = $cacheResult['return'];
           $this->menu  = $cacheResult['menu'];
           $this->found = $cacheResult['found'];
        }

        return $return;
    }
}