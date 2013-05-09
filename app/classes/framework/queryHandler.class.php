<?php

use u4u\db_mysqli;
/**
 * @author unreal4u
 *
 */
abstract class queryHandler extends db_mysqli {
    protected function generateInsertQuery() {

    }

    /**
     * From the object itself, generates an insert or update query
     */
    private function generateQuery() {
        //return 'INSERT INTO '.$this->_tableName.' () VALUES () ON DUPLICATE KEY UPDATE SET ';
    }


}
