<?php
/**
 * Miscelanious functions that are used throughout the system
 *
 * @package Internals
 * @author Camilo Sperberg
 * @copyright 2010 - 2011 Camilo Sperberg
 */
class misc {
    private $db;
    private $he;
    // @TODO get $r out of here
    private $r;

    public function __construct($db=null, $he=null) {
        if (!is_null($db)) {
            $this->db = $db;
        }
        if (!is_null($he)) {
            $this->he = $he;
        }
        // @TODO Get $r out of here
        global $r;
        $this->r = & $r;
    }

    /**
     * This function generates a password salt as a string of x (default = 15) characters ranging from a-zA-Z0-9.
     *
     * @author AfroSoft <scripts@afrosoft.co.cc>
     *
     * @param int $max The number of characters in the string
     * @return string The generated salt
     */
    private function generateSalt($max=32) {
        $characterList = "<>|abcdefghijklmnopqrstuvwxyz\\!#$%ABCDEFGHIJKLMNOPQRSTUVWXYZ_-/&()0123456789.[]{}";
        $i = 0;
        $salt = "";
        do {
            $salt .= $characterList{mt_rand(0, strlen($characterList) - 1)};
            $i++;
        } while ($i <= $max);
        return md5((time() + microtime()) . PASSWD_HASH . $salt);
    }

    /**
     * Generates a secure password from user input
     *
     * @param string $passwd The password user created
     * @return array The encrypted password and the hash for the password
     */
    public function createPassword($passwd) {
        $passwd = trim(htmlentities($passwd));
        $hash = $this->generateSalt();
        return array(
            'passwd' => md5(substr($passwd, 0, round(strlen($passwd) / 2)) . $hash . substr($passwd, round(strlen($passwd) / 2))),
            'hash' => $hash,
        );
    }

    /**
     * Redirects the user to another page
     *
     * @param string $where Where to redirect
     * @param string $message The message to display to user
     * @param int $type The type of redirect. Choose between 301 and 302. Defaults to 302
     */
    public function redir($where='', $message='', $type = 302) {
        if (strpos($where, 'http://') === FALSE) {
            $where = HOME . $where;
        }
        if (isset($_SESSION['user_id'])) {
            $this->logActivity($_SESSION['user_id'], 'red', 'Type: ' . $type . '<br /><strong>Old</strong>: ' . $_SERVER['REQUEST_URI'] . '<br /><strong>New</strong>: ' . $where);
        }

        // Fix for certain Firefox versions problem
        header('Pragma: no-cache');
        header('Cache-Control: no-cache');

        // The redirect
        header('Location: ' . $where, true, $type);

        // Don't forget to die before the redirect, display message if for some reason redirect don't work
        exit($message);
    }

    /**
     * Imprime el título y subtítulo de la página
     *
     * @param $titulo string
     * @param $descripcion string
     * @param $print bool
     * @return string
     */
    public function c_title($title = '', $description = '', $print = false) {
        $output = '';
        if (!empty($title)) {
            $output = $this->he->c_tag('h1', $title);
            if (!empty($description))
                $output .= $this->he->c_tag('h2', $description, 'note');
            if (empty($this->r['tit'])) {
                $this->r['tit'] = $title;
            }
            if ($print === true) {
                echo $output;
            }
        }
        return $output;
    }

    /**
     * Convierte una mac en formato hexadecimal a su representación humana
     *
     * @param $lamac string
     * @return string
     */
    public function hex2mac($lamac = '') {
        $mactmp = strlen($lamac);
        if (strlen($lamac) < 12) {
            $previo = '';
            for ($i = 0; $i < (12 - strlen($lamac)); $i++)
                $previo .= '0';
            $lamac = $previo . $lamac;
            unset($previo);
        }
        $lamac = str_split($lamac, 2);
        $mac = '';
        foreach ($lamac as $b)
            $mac .= $b . ':';
        $mac = substr($mac, 0, -1);
        return $mac;
    }

    /**
     * Function that logs activity into db
     *
     * @param $user int ID of user
     * @param $key char(3) ID in form of char(3) of the type of logging
     * @param $value string Value of what you want to log
     */
    public function logActivity($user, $key, $value) {
        $res = $this->db->query('INSERT INTO sist_activity(id_user,k,v,session) VALUES (?,?,?,?)', $user, $key, $value, session_id());
        if ($res !== false) {
            $res = true;
        }
        return $res;
    }

    public function getFilteredDirContentArray($rootPath, $directoriesOnly = false, $extensionsArray = array(), $recursive = false) {
        $return = array();
        //force dir to end with /
        $rootPath = rtrim($rootPath, '/') . '/';
        $pregMatchPattern = '';
        if (!empty($extensionsArray)) {
            $pregMatchPattern = '/';
            foreach ($extensionsArray as $extension) {
                $pregMatchPattern .= '\.' . $extension . '$|';
            }
            $pregMatchPattern = substr($pregMatchPattern, 0, -1) . '/';
        }

        if ((!empty($pregMatchPattern) or $directoriesOnly === true) and is_dir($rootPath)) {
            $childs = scandir($rootPath, 0);
            foreach ($childs as $child) {
                if (!empty($recursive) and is_dir($rootPath . $child) && substr($child, 0, 1) != '.') {
                    $childDirectory = $this->getFilteredDirContent($rootPath . $child, $directoriesOnly, $extensionsArray, true);
                    if (!empty($childDirectory)) {
                        $return[$child] = $childDirectory;
                    }
                } else {
                    if (!empty($directoriesOnly)) {
                        if (is_dir($rootPath . $child) and substr($child, 0, 1) != '.') {
                            $return[] = $child;
                        }
                    } elseif (preg_match($pregMatchPattern, $child)) {
                        $return[] = $child;
                    }
                }
            }
        }
        return $return;
    }

    /**
     * Scan a directory and output its contents as an array with routes
     *
     * @author Camilo Sperberg - http://unreal4u.com/
     *
     * @param string $rootDir
     * @param array $allData
     * @param array $extensionsArray Rescue only the given extensions
     * @return array Returns array with data
     */
    public function getFilteredDirContentString($rootDir, array $allData=array(), array $extensionsArray=array()) {
        $rootDir = rtrim($rootDir, '/') . '/';
        $pregMatchPattern = '';
        if (!empty($extensionsArray)) {
            $pregMatchPattern = '/';
            foreach ($extensionsArray as $extension) {
                $pregMatchPattern .= '\.' . $extension . '$|';
            }
            $pregMatchPattern = substr($pregMatchPattern, 0, -1) . '/';
        }

        $dirContent = scandir($rootDir);
        foreach($dirContent as $content) {
            $path = $rootDir.$content;
            if(is_file($path) && preg_match($pregMatchPattern, $content) && is_readable($path)) {
                $allData[] = $path;
            } elseif(substr($content, 0, 1) != '.') {
                $allData = $this->getFilteredDirContentString($path, $allData, $extensionsArray);
            }
        }

        return $allData;
    }
}
