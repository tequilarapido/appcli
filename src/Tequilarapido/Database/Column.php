<?php namespace Tequilarapido\Database;

use Tequilarapido\Database\Database;

class Column
{
    static $db_primarykeys = array();

    public function scanTextColumns($database, $exclude = array())
    {
        // Text Columns
        $db_text_columns = $this->textColumns($database);

        $db_columns = array();
        foreach ($db_text_columns as $column) {
            if (is_numeric($column['TABLE_NAME'])) {
                continue;
            }

            if (in_array($column['TABLE_NAME'], $exclude)) {
                continue;
            }

            if (empty($db_columns[$column['TABLE_NAME']])) {
                $db_columns[$column['TABLE_NAME']] = array();
                $db_columns[$column['TABLE_NAME']]['columns'] = array();
                $db_columns[$column['TABLE_NAME']]['pk'] = $this->primaryKey($column['TABLE_NAME']);
            }

            $db_columns[$column['TABLE_NAME']]['columns'][] = $column['COLUMN_NAME'];
        }

        return $db_columns;
    }

    public function primaryKey($table)
    {
        // Have we already a PK for this table
        if (!empty(static::$db_primarykeys[$table])) {
            return static::$db_primarykeys[$table];
        }

        $query = "SHOW KEYS FROM $table ";
        $results = Database::select($query);
        $keys = array();
        foreach ($results as $row) {
            if ($row['Key_name'] == 'PRIMARY') {
                $keys[$row['Seq_in_index'] - 1] = $row['Column_name'];
            }
        }

        static::$db_primarykeys[$table] = current($keys);
        return static::$db_primarykeys[$table];
    }

    public static function textColumns($database)
    {
        $query = "SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS ";
        $query .= "WHERE 1 ";
        $query .= "AND TABLE_SCHEMA = ? ";
        $query .= "AND ( DATA_TYPE LIKE '%char%' OR DATA_TYPE LIKE '%text%' OR DATA_TYPE LIKE '%BLOB%' )";

        return Database::select($query, array($database));
    }

}