<?php

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
     * Contains the current session id
     * @var string
     */
    public $session_id = '';

    /**
     * Whether the user is logged in or not. Defaults to false
     * @var boolean
     */
    public $loggedIn = false;

    /**
     * The current logged in user name
     * @var string
     */
    public $loginUsername = '';
    /**
     * The current session id
     * @var string
     */
    public $sessionId = '';

    /**
     * Internal pointer to know whether the requested page has been found or not. Don't mess with this
     * @var boolean
     */
    public $found = false;
    public $id_user;

    /**
     * Contains the left menu
     * @var array
     */
    public $menu = array();

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
     * Don't remember, fill in later
     * @var unknown
     */
    public $development;

    /**
     * Autoloader and instantiator of u4u classes
     * @var object
     */
    private $u4uAutoLoader = null;

    /**
     * Constructor
     */
    public function __construct() {
        $this->timeRequestBegin = microtime(true);

        $this->locale = setlocale(LC_ALL, 'es_ES', 'es_CL', 'es', 'ES' );
        date_default_timezone_set('America/Santiago');

        spl_autoload_register(array(
            $this,
            'autoloadHandler'
        ));
    }

    /**
     * Initialize the most basic classes and the session also
     */
    public function initializeMainObject() {
        $this->includeThirdparty(U4U_CLASSES);
        $this->u4uAutoLoader   = new \u4u\autoLoader();
        $this->db              = $this->u4uAutoLoader->instantiateClass('db_mysqli');
        $this->db->keepLiveLog = true;

        $this->initializeSession();
        $this->registerBasicClasses();
    }

    /**
     * Initializes the session
     */
    private function initializeSession() {
        session_cache_limiter('private');
        ini_set("session.gc_maxlifetime", SESION_EXPIRE);
        ini_set("session.entropy_file", "/dev/urandom");
        ini_set("session.entropy_length", "512");
        session_cache_expire(CACHE_EXPIRE);
        session_name(SESION_NAME);
        session_start();
        if (empty($_SESSION['timeout'])) {
            $this->db->query('INSERT INTO sist_sessions (id_session,ip,useragent) VALUES (?,INET_ATON(?),?)', session_id(), $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
            $_SESSION['timeout'] = time() + SESION_EXPIRE;
        }
    }

    /**
     * The basic classes with which this framework works
     */
    private function registerBasicClasses() {
        $this->cache    = $this->u4uAutoLoader->instantiateClass('cacheManager' , array('apc'));
        if (APP_ENVIRONMENT != 'production') {
            //$this->cache->enableDebugMode();
        }
        $this->he       = $this->u4uAutoLoader->instantiateClass('HTMLUtils');
        $this->misc     = new misc($this->db, $this->he);
        $this->css      = $this->u4uAutoLoader->instantiateClass('csstacker');
        $this->msgStack = new messageStack();
        $this->bc       = new breadcrump();
        $this->view     = new view();
    }

    /**
     * The autoloader of the framework
     * @param string $class The class name we want to load
     * @return boolean Returns true if class could be included, false otherwise
     */
    private function autoloadHandler($class) {
        $return = true;
        if (is_readable(CLASSES . $class . '.class.php')) {
            include (CLASSES . $class . '.class.php');
        } else {
            $return = false;
        }
        return $return;
    }

    /**
     * Includes a third party class from the thirdparty directory
     * @param constant $thirdPartyId The constant name we are trying to include
     */
    public function includeThirdparty($thirdPartyId='') {
        include (THIRDPARTY_DIRECTORY . $thirdPartyId);
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
     * @param string $uri The route the user requested
     * @return string The definite route of the file we want to load
     */
    public function validateRoute($uri='') {
        $return = '';

        #$allUris = $this->cache->load('uriMapping');
        $allUris = false;

        #$uri = 'no-permission';
        $uriValidator = new uriValidator($uri);

        debug($uriValidator->loadThis);

        die('dying... '.__FILE__.':'.__LINE__);
        if ($allUris === false OR !array_key_exists($uri, $allUris)) {
            $this->getModules();

            if (empty($uri)) {
                $uri = HOMEPAGE;
            }

            $uri = trim($uri, '/');

            if (in_array($uri.'.php', $this->modules)) {
                $return = $uri;
            } else {
                if (in_array('index/'.$uri.'.php', $this->modules)) {
                    $return = 'index/'.$uri;
                } else {
                    $workingUri = $uri;
                    if (substr_count($workingUri, '/') < 1) {
                        $workingUri .= '/index';
                    }

                    if (in_array($workingUri.'.php', $this->modules)) {
                        $return = $workingUri;
                    } else {
                        $return = 'index/not-found';
                    }
                }
            }

            if (strpos($return, 'ajax/') !== false) {
                $this->isAjaxRequest = true;
            }

            $allUris[$uri] = $return;
            $this->cache->save($allUris, 'uriMapping', array(), 3600);
        } else {
            $return = $allUris[$uri];
        }

        $this->module  = $return;
        $this->request = $uri;
        if (!empty($this->module)) {
            $this->myHome = $this->request.'/';
        }

        return $return;
    }

    /**
     * Gets a list of all modules in the system
     */
    private function getModules() {
        $this->modules = $this->cache->load('activeModules');
        if ($this->modules === false) {
            $this->modules = $this->misc->getFilteredDirContentString(CONTROLLERS, array(), array('php'));
            $this->modules = str_replace(CONTROLLERS, '', $this->modules);
            $this->cache->save($this->modules, 'activeModules', array(), 3600);
        }

        return $this->modules;
    }

    /**
     * Loads all options from the database or cache
     *
     * @return array Returns an array with all options
     */
    public function loadOptions() {
        $this->options = $this->cache->load('siteOptions');
        if (empty($this->options)) {
            $aOptions = $this->db->query('SELECT name AS option_name,v FROM sist_options WHERE id_option = ?', 'sop');
            if ($this->db->num_rows > 0) {
                foreach ($aOptions as $a) {
                    $this->options[$a['option_name']] = $a['v'];
                }
            }
            unset($aOptions, $a);
            $this->cache->save($this->options, 'siteOptions', array(), 3600);
        }

        return $this->options;
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

        $cacheResult = $this->cache->load('leftmenu', array('groupString' => $groupString));
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
                'leftmenu',
                array('groupString' => $groupString),
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