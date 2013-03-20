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
abstract class databaseModel {
    /**
     * Constant that holds the current table name
     *
     * @var string
     */
    const TABLE_NAME = 0;

	/**
	 * Table definition
	 *
	 * @var array
	 */
	protected $fields = array();

	/**
	 * The initial data with which to populate our table
	 *
	 * @var array
	 */
	protected $initialData = array();

	/**
	 * What is the class name we are working with
	 *
	 * @var string
	 */
	private $extendingClassName = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->extendingClassName = get_called_class();
		if (!is_string($this->extendingClassName::TABLE_NAME)) {
		    throw new Exception('Table name must be defined!');
		}
	    $this->fields = array_merge($this->fields, $$this->extendingClassName->fields);
	}

	/**
	 * Method that check differences between current table
	 */
	public function systemInstall($fields=array()) {
		$previousTable = '';
		foreach($fields AS $table => $field) {
			if (!empty($previousTable)) {
				$sql = ' AFTER '.$previousTable;
				$previousTable = $table;
			}
		}

		if ($isNewTable === true) {
			$this->insertArrayData($initialData);
		}
	}

	/**
	 * Formats data in array form into the object
	 */
	public function insertArrayData($initialData) {
		$object = new $$this->extendingClassName();
		foreach($initialData AS $values) {
			$object->loadByArrayRow($values);
			return $object->save();
		}
	}
}

