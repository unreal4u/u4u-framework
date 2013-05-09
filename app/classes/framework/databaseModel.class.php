<?php
use \u4u\db_mysqli;
use \u4u\cacheManager;

class fieldNotExistsException extends \Exception {}

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
        $this->_fillBasics();
        // 99% of the time we won't need these fields, so unset them immediatly
        unset($this->_fields, $this->fields, $this->_initialData);
    }

    private function _fillBasics() {
        $this->_extendingClassName = get_called_class();
        $cacheManager = new cacheManager('apc');
        $databaseDDL = $cacheManager->load('u4u-databaseDDL', array('u4u-internals', 'class' => $this->_extendingClassName));
        if ($databaseDDL === false) {
            $databaseDDL = array();
            $rc = new \ReflectionClass($this->_extendingClassName);
            // Set the table name, first from constant, if that doesn't work, grab it from the class's name
            $databaseDDL['tableName'] = $rc->getConstant('TABLE_NAME');
            if (empty($databaseDDL['tableName'])) {
                $databaseDDL['tableName'] = $this->_extendingClassName;
            }

            // Set the fields
            $databaseDDL['fields'] = $this->_fields + $this->fields;
            $cacheManager->save($databaseDDL, 'u4u-databaseDDL', array('u4u-internals', 'class' => $this->_extendingClassName), 86400);
        }

        $this->_tableName = $databaseDDL['tableName'];
        $this->_fields = $databaseDDL['fields'];
        $this->_fillDefaultsFields();
        return true;
    }

    /**
     * Fills in all fields of the object with the object's default values
     */
    private function _fillDefaultsFields() {
        foreach($this->_fields AS $field => $value) {
            if (!isset($value['DEFAULT'])) {
                $value['DEFAULT'] = null;
            }
            $this->databaseFields[$field] = $value['DEFAULT'];
        }

        return true;
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
    public function systemInstall() {
        // First of all, delete all data from cache and regenerate it
        $cacheManager = new cacheManager('apc');
        $cacheManager->purgeIdentifierCache('u4u-databaseDDL', array('u4u-internals', 'class' => $this->_extendingClassName));
        $this->_fillBasics();

        $previousTable = '';
        foreach ($this->_fields as $table => $field) {
            if (!empty($previousTable)) {
                $sql = ' AFTER ' . $previousTable;
                $previousTable = $table;
            }
        }

        $isNewTable = false;
        if ($isNewTable === true) {
            $this->insertArrayData($this->_initialData);
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

    /**
     * Saves the object
     *
     * @return boolean Returns true on success save, false otherwise
     */
    public function save() {
        return true;
    }
}

