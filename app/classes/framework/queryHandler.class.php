<?php

/**
 * @author unreal4u
 *
 */
abstract class queryHandler extends \u4u\db_mysqli {
    protected function generateInsertQuery() {

    }

    /**
     * From the object itself, generates an insert or update query
     */
    protected function generateQuery() {
        return 'INSERT INTO '.$this->_tableName.' () VALUES () ON DUPLICATE KEY UPDATE SET ';
    }
}
