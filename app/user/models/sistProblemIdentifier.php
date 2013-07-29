<?php

namespace models;

/**
 * Module description
 *
 * @package Models
 * @author Camilo Sperberg
 * @license BSD License. Feel free to use and modify
 */
class sistProblemIdentifier extends \databaseModel {
    const TABLE_NAME = 'sist_problemIdentifier';

    protected $fields = array(
        'id' 	  => array('type' => 'INT(8)',      'NULL' => false, 'DEFAULT' => NULL, 'UNSIGNED' => true, 'INDEXES' => array('PRIMARY' => 1)),
        'type' 	  => array('type' => 'VARCHAR(32)', 'NULL' => false, 'DEFAULT' => '',),
        'message' => array('type' => 'MEDIUMTEXT',  'NULL' => true,  'DEFAULT' => NULL,),
    );

    /**
     * Example of a method in this model
     *
     * @param string $type
     * @param number $message
     * @return boolean Returns true if object could be saved, false otherwise
     */
    public function addProblem($type, $message) {
        $this->type = $type;
        $this->message = $message;
        return $this->save();
    }
}
