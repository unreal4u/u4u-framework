<?php
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
abstract class databaseModel extends queryHandler {
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
     * Contains a small list of the insertable fields
     * @var array
     */
    protected $_insertFields = array();

    /**
     * Contains a small list with the primary key fields of the
     * @var unknown
     */
    protected $_primaryKeyFields = array();

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
            foreach($databaseDDL['fields'] AS $fieldName => $fieldSettings) {
                $databaseDDL['insertFields'][] = $fieldName;
                if (!empty($fieldSettings['INDEXES']) && array_key_exists('PRIMARY', $fieldSettings['INDEXES'])) {
                    $databaseDDL['primaryKey'][] = $fieldName;
                }
            }

            // @TODO 2013-05-09 Enable cache!
            #$cacheManager->save($databaseDDL, 'u4u-databaseDDL', array('u4u-internals', 'class' => $this->_extendingClassName), 86400);
        }

        $this->_tableName        = $databaseDDL['tableName'];
        $this->_fields           = $databaseDDL['fields'];
        $this->_primaryKeyFields = $databaseDDL['primaryKey'];
        $this->_insertFields     = $databaseDDL['insertFields'];
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
     * Creates or edits an LIMIT statement. Deals with unclean data.
     *
     * Valid limit and offsets for MySQL are positive integers. So, if a float comes in, this function will round it. If
     * a negative number is entered, it will be ignored, as it will be if we enter a string instead of a numeric
     * character. Also, if we want to insert an offset, we MUST have a limit.
     *
     * @param int $amount The limit we want to rescue. No default value
     * @param int $offset The offset we want to begin from. Default value: 0
     * @return string Returns a formatted string according to input
     */
    public function fixLimit($amount=null, $offset=0) {
        $return = '';

        if (is_numeric($amount) && $amount >= 0) {
            $return = ' LIMIT '.round($amount);

            // An offset REQUIRES a limit set, so check only if a limit is set
            if (is_numeric($offset) && !empty($offset) && $offset >= 0) {
                $return .= ' OFFSET '.round($offset);
            }
        }

        return $return;
    }

    /**
     * Saves the object
     *
     * @return boolean Returns true on success save, false otherwise
     */
    public function save() {
        $query = $this->generateQuery();
        #return $this->insert_id($query, $this->databaseFields);
        return true;
    }
}

