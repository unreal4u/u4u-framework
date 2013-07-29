<?php

/**
 * @author unreal4u
 *
 */
abstract class queryConstructor extends \u4u\db_mysqli {

    private function _filterKeyword($keyword, $string) {
        $return = '';

        if (!empty($string)) {
            $keyword = trim(strtoupper($keyword));
            switch ($keyword) {
                case 'WHERE':
                case 'ORDER BY':
                case 'GROUP BY':
                case 'HAVING':
                    $replace = $keyword.' ';
                    break;
                default:
                    $replace = '';
                    break;
            }

            $return = trim(str_ireplace($replace, '', $string));
        }

        return $return;
    }

    /**
     * Creates the where part of the query
     *
     * @param string $where
     * @param string $key
     * @param string $connector
     * @param string $operation
     * @throws \Exception
     * @return string
     */
    final protected function _constructWhere($where, $key, $connector='AND', $operation='=') {
        // @TODO Check for some more connectors I could be forgetting (such as && and ||, etc)
        if ($connector != 'AND' && $connector != 'OR') {
            throw new \Exception('Incorrect connector, choose between AND or OR');
        }

        $where = $this->_filterKeyword('WHERE', $where);
        if (!empty($where)) {
            $where = '('.$where.') '.$connector.' ';
        }
        $where .= '('.$key.' '.$operation.' ?)';

        return 'WHERE '.$where;
    }

    /**
     * Constructs an insert query
     *
     * @param boolean $autoUpdate Whether to append ON DUPLICATE KEY UPDATE string or not. Defaults to "false"
     */
    final protected function _constructInsert($tableName, $autoUpdate=true) {
        $return = array();

        $query  = 'INSERT INTO `'.$tableName.'` (';
        $values = '';
        $duplicate = '';
        $returnValues1 = $returnValues2 = array();

        /*
         * Ideal case scenario, but will have to rewrite db_mysqli class for this to use PDO:
        */
        /*
         foreach ($this->databaseFields as $field => $value) {
        $return[':'.$field] = $value;
        $query  .= '`'.$field.'`,';
        $values .= ':'.$field.',';
        $duplicate .= '`'.$field.'`=:'.$field.',';
        }
        */
        foreach ($this->databaseFields as $field => $value) {
            $returnValues1[] = $value;
            $query .= '`'.$field.'`,';
            $values .= '?,';
            if ($autoUpdate === true) {
                $duplicate .= '`'.$field.'`=?,';
                $returnValues2[] = $value;
            }
        }


        $return[0] = substr($query, 0, -1).') VALUES ('.substr($values, 0, -1).')';
        foreach($returnValues1 AS $returnValue) {
            $return[] = $returnValue;
        }

        if ($autoUpdate === true) {
            $return[0] .= ' ON DUPLICATE KEY UPDATE '.substr($duplicate, 0, -1);
            foreach($returnValues2 as $returnValue) {
                $return[] = $returnValue;
            }
        }

        return $return;
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
    final protected function _constructLimit($amount=null, $offset=0) {
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
     * Creates an order by or group by part of the query
     *
     * @TODO Support for ASC or DESC
     */
    final protected function _constructOther($orderBy, $fields, $type='ORDER BY') {
        if ($type != 'ORDER BY' && $type != 'GROUP BY') {
            throw new \Exception('Must be ORDER BY or GROUP BY');
        }

        if (is_string($fields)) {
            $fields = array($fields);
        }

        $orderBy = $this->_filterKeyword($type, $orderBy);

        foreach ($fields as $field) {
            $orderBy .= '`'.$field.'`,';
        }

        if (!empty($orderBy)) {
            $orderBy = substr($orderBy, 0, -1);
        }

        return $orderBy;
    }
}
