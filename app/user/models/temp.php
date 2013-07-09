<?php
/**
 * Module description
 *
 * @package General
 * @version $Rev$
 * @copyright $Date$
 * @author $Author$
 * @license BSD License. Feel free to use and modify
 */

class temp extends databaseModel {
    protected $fields = array(
        'id_user' 		=> array('type' => INT, 		'length' => 11, 					 										'auto_increment' => TRUE, 	'unsigned' => TRUE, 	'zerofill' => FALSE, 																'INDEXES' => array('PRIMARY'), 										),
        'login'			=> array('type' => VARCHAR, 	'length' => 24, 																					 	'unsigned' => FALSE, 	'zerofill' => FALSE, 	'charset' => 'utf8', 	'collation' => 'utf8_general_ci', 	'INDEXES' => array('UNI_login'),									),
        'passwd'		=> array('type' => CHAR, 		'length' => 32,																						 	'unsigned' => FALSE, 	'zerofill' => FALSE, 	'charset' => 'ascii',	'collation' => 'ascii_bin',																				),
        'salt_hash'		=> array('type' => CHAR, 		'length' => 32, 	'NULL' => FALSE, 	'default' => '', 																								'charset' => 'ascii',	'collation' => 'ascii_bin',											'comment' => 'A random salt hash',	),
        'first_name' 	=> array('type' => VARCHAR,		'length' => 96,		'NULL' => TRUE, 																																																													),
        'last_name'		=> array('type' => VARCHAR, 	'length' => 128,	'NULL' => TRUE,																																																														),
        'created'		=> array('type' => TIMESTAMP, 					 	'NULL' => FALSE, 	'default' => 'CURRENT_TIMESTAMP',																																																				),
        'active'		=> array('type' => BIT, 							'NULL' => FALSE, 	'default' => 'TRUE', 																																																							),
    );
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