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

class sistProblemIdentifier extends databaseModel {
    const TABLE_NAME = 'sist_problemIdentifier';

	protected $attributes = array(
		'id' 	=> array('type' => 'INT(8)', 'NULL' => false, 'DEFAULT' => '0', 'UNSIGNED' => true, 'INDEXES' => array('PRIMARY' => 1)),
		'type' 	=> array(),
	);

	public function addProblem($type='', $message=3) {
		$this->type = $type;
		$this->message = $message;
		$this->save();
	}
}