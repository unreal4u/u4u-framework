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
            $this->login = $username;
            $this->active = 1;

            $databaseReturnCall = $this->loadByProperties();
            if (!empty($databaseReturnCall)) {
                foreach ($databaseReturnCall[0] as $key => $value) {
                    $this->$key = $value;
                }
            }

            if ($adminOverwrite === false) {
                $password = $this->generateEncryptedPassword($password);
                if ($this->generateEncryptedPassword($password) != $this->passwd) {
                    $this->_fillDefaultsFields();
                }
            }
        }

        return $this;
    }

    /**
     * This function generates a password salt as a string of x characters ranging from CHR(32) until CHR(125).
     *
     * @author AfroSoft <scripts@afrosoft.co.cc>
     * @author Camilo Sperberg - Uses chr(32..125) instead of character list
     *
     * @param int $max The number of characters in the string. Defaults to 32
     * @return users
     */
    protected function _generateSaltHash($max=32) {
        $i = 0;
        $salt = "";
        do {
            $salt .= chr(mt_rand(32, 125));
            $i++;
        } while ($i <= $max);

        return md5((string)microtime(true).PASSWD_HASH.$salt);
    }

    /**
     * Generates a secure password from user input
     *
     * @param string $passwd The password user created
     * @return users
     */
    public function generateEncryptedPassword($passwd) {
        $hash = $this->_generateSaltHash(32);
        return md5(substr($passwd, 0, round(strlen($passwd) / 2)).$hash.substr($passwd, round(strlen($passwd) / 2)));
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