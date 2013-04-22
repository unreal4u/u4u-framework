<?php
use \u4u\db_mysqli;

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
     * Holds the current table name
     *
     * @var string
     */
    private $_tableName = '';
    /**
	 * Table definition
	 *
	 * @var array
	 */
    protected $_fields = array();
    /**
	 * The initial data with which to populate our table
	 *
	 * @var array
	 */
    protected $_initialData = array();
    /**
	 * What is the class name we are working with
	 *
	 * @var string
	 */
    private $_extendingClassName = '';

    /**
     * The actual fillable database fields
     * @var array
     */
    protected $databaseFields = array();

    /**
	 * Constructor
	 */
    public function __construct() {
            $this->_extendingClassName = get_called_class();
        $rc = new \ReflectionClass($this->_extendingClassName);
        if (!$rc->isSubclassOf('databaseModel')) {
            $errorMessage = 'Class doesn\'t extend databaseModel, aborting creation';
            #if ($this->throwExceptions === true) {
            #    throw new \u4u\cacheException($errorMessage);
            #}
            #trigger_error($errorMessage, E_USER_ERROR);
        }
        $this->_tableName = $rc->getConstant('TABLE_NAME');
        $this->_fields = $this->_fields + $this->fields;
        foreach($this->_fields AS $field => $value) {
            if (isset($value['DEFAULT'])) {
                $this->databaseFields[$field] = $value['DEFAULT'];
            } else {
                $this->databaseFields[$field] = null;
            }
        }
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name='', $value=null) {
        if (!empty($name)) {
            $this->databaseFields[$name] = $value;
        }
    }

    /**
     * Getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name='') {
        $return = null;

        if (isset($this->databaseFields[$name])) {
            $return = $this->databaseFields[$name];
        }

        return $return;
    }

    /**
	 * Method that check differences between current table
	 */
    public function systemInstall($fields = array()) {
        $previousTable = '';
        foreach ($fields as $table => $field) {
            if (!empty($previousTable)) {
                $sql = ' AFTER ' . $previousTable;
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
        foreach ($initialData as $values) {
            $object->loadByArrayRow($values);
            return $object->save();
        }
    }

    public function save() {
        return true;
    }
}

