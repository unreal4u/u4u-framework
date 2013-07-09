<?php

namespace models;

/**
 * Module description
 *
 * @package General
 * @author Camilo Sperberg
 * @license BSD License. Feel free to use and modify
 */

class users extends \databaseModel {
    const TABLE_NAME = 'sist_users';

    protected $fields = array(
        'id_user' 		=> array('type' => 'INT(11)', 						 										'auto_increment' => TRUE, 	'unsigned' => TRUE, 	'zerofill' => FALSE, 																'INDEXES' => array('PRIMARY' => 1), 	'comment' => NULL,	),
        'login'			=> array('type' => 'VARCHAR(24)', 																					 	'unsigned' => FALSE, 	'zerofill' => FALSE, 	'charset' => 'utf8', 	'collation' => 'utf8_general_ci', 	'INDEXES' => array('UNI_login'),	    'comment' => NULL,	),
        'passwd'		=> array('type' => 'CHAR(32)', 																						 	'unsigned' => FALSE, 	'zerofill' => FALSE, 	'charset' => 'ascii',	'collation' => 'ascii_bin',					    						    'comment' => NULL,	),
        'salt_hash'		=> array('type' => 'CHAR(32)', 		'NULL' => FALSE, 	'default' => '', 																								'charset' => 'ascii',	'collation' => 'ascii_bin',						   					        'comment' => NULL,	),
        'first_name' 	=> array('type' => 'VARCHAR(96)',	'NULL' => TRUE, 																																																   				        'comment' => NULL,	),
        'last_name'		=> array('type' => 'VARCHAR(128)', 	'NULL' => TRUE,																																																		  			        'comment' => NULL,	),
        'created'		=> array('type' => 'TIMESTAMP', 	'NULL' => FALSE, 	'default' => 'CURRENT_TIMESTAMP',																																									   		        'comment' => NULL, 	),
        'active'		=> array('type' => 'BIT', 			'NULL' => FALSE, 	'default' => TRUE,                                                                                                                                                                                                  'comment' => NULL,  ),
    );

    public function tryLogin($username='', $password='', $adminOverwrite=false) {
        if (!empty($username)) {
            if ($adminOverwrite === false) {
                $query = 'SELECT * FROM users WHERE login = ? AND passwd = ?';
            } else {
                $query = 'SELECT * FROM users WHERE login = ?';
            }
        }
    }

    /**
     * This function generates a password salt as a string of x (default = 15) characters ranging from a-zA-Z0-9.
     *
     * @author AfroSoft <scripts@afrosoft.co.cc>
     * @author Camilo Sperberg - Uses chr(32..125) instead of character list
     *
     * @param int $max The number of characters in the string
     * @return string The generated salt
     */
    public function generateSalt($max=32) {
        $i = 0;
        $salt = "";
        do {
            $salt .= chr(mt_rand(32, 125));
            $i++;
        } while ($i <= $max);
        return md5((string)microtime(true) . PASSWD_HASH . $salt);
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
}



/*
 * CREATE TABLE sist_users (
    id_user         int(11) AUTO_INCREMENT NOT NULL,
    login           varchar(24) CHARSET utf8 COLLATE utf8_bin NOT NULL,
    passwd          char(32) CHARSET ASCII COLLATE ascii_bin NOT NULL,
    id_empresa      int(11) UNSIGNED NOT NULL DEFAULT '1',
    first_name      varchar(96) NULL,
    last_name       varchar(128) NULL,
    created         timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    active          tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
    salt_hash       char(32) NOT NULL,
    PRIMARY KEY(id_user),
    UNIQUE KEY(login)
);

 */