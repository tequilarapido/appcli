<?php namespace Tequilarapido\Database;

use Tequilarapido\Database\Database;

class Table
{
    /**
     * Return database tables
     * @param $database
     * @return array of tables
     */
    public function all($database)
    {
        $query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES ";
        $query .= "WHERE 1 ";
        $query .= "AND TABLE_SCHEMA = ? ";
        $result = Database::select($query, array($database));

        $all = array();
        foreach ($result as $t) {
            $all[] = $t['TABLE_NAME'];
        }
        return $all;
    }

    public function filterByTruncateConf($database, $cleanupConf, $prefix)
    {
        $tables = array();

        // Get all tables
        $all = $this->all($database);

        // Add simple tables
        if (!empty($cleanupConf->simple) && is_array($cleanupConf->simple)) {
            foreach ($cleanupConf->simple as $t) {
                if (in_array($t, $all)) {
                    $tables[] = $t;
                }
            }
        }

        // Add multi tables
        if (!empty($cleanupConf->multi) && is_array($cleanupConf->multi)) {
            foreach ($cleanupConf->multi as $t) {
                $matchedTables = $this->getTablesForMulti($t, $all, $prefix);
                if (!empty($matchedTables)) {
                    $tables = array_merge($tables, $matchedTables);
                }
            }
        }

        return array_unique($tables);
    }

    public function getTablesForMulti($table, $prefix, $all = array())
    {
        $pattern = '/^' . $prefix . '([0-9]+_)?' . $table . '$/';
        return preg_grep($pattern, $all);
    }

    public function filterByDeleteConf($database, $cleanupConf, $prefix)
    {
        $tables = array();

        // Get all tables
        $all = $this->all($database);

        // Add simple tables
        if (!empty($cleanupConf->simple) && is_array($cleanupConf->simple)) {
            foreach ($cleanupConf->simple as $t) {
                if (in_array($t, $all)) {
                    $tables[] = $t;
                }
            }
        }

        // Add multi tables
        if (!empty($cleanupConf->multi) && is_array($cleanupConf->multi)) {
            foreach ($cleanupConf->multi as $t) {
                $pattern = '/^' . $prefix . '([0-9]+_)?' . $t . '$/';
                $matchedTables = preg_grep($pattern, $all);
                if (!empty($matchedTables)) {
                    $tables = array_merge($tables, $matchedTables);
                }
            }
        }

        return array_unique($tables);
    }


    /**
     * @param $database
     * @return array of tables sizes
     */
    public function sizes($database)
    {
        $query = '';
        $query .= 'SELECT TABLE_NAME, table_rows, data_length, index_length   ';
        $query .= 'FROM information_schema.TABLES WHERE table_schema = ? ';


        $res = Database::select($query, array($database));

        $total_data = $total_index = $total = 0;
        foreach ($res as &$item) {
            $item['data_length'] = round($item['data_length'] / 1024 / 1024, 2);
            $item['index_length'] = round($item['index_length'] / 1024 / 1024, 2);
            $item['total'] = $item['data_length'] + $item['index_length'];

            // Totals
            $total_data += $item['data_length'];
            $total_index += $item['index_length'];
            $total += $item['data_length'] + $item['index_length'];
        }


        // Add total at the bootom
        if (!empty($res)) {
            $res[] = array(
                'TABLE_NAME' => 'total',
                'table_rows' => '-',
                'data_length' => $total_data,
                'index_length' => $total_index,
                'total' => $total
            );
        }


        usort($res, function ($a, $b) {
            return $a['total'] > $b['total'];
        });

        return $res;
    }

    /*
    |--------------------------------------------------------------------------
    | Engines
    |--------------------------------------------------------------------------
    */

    public function alterEngine($table, $engine)
    {
        $query = "ALTER TABLE $table ENGINE = $engine";
        $res = Database::statement($query);
        if (!$res) {
            throw new \Exception("Query failed : $query");
        }
    }

    public function getEngines($database)
    {
        $query = '';
        $query .= 'SELECT table_name, engine ';
        $query .= 'FROM   information_schema.tables ';
        $query .= 'WHERE table_schema = ? ';

        return Database::select($query, array($database));
    }

    public function isEngine($database, $engine)
    {
        $tablesEngines = $this->getEngines($database);

        $notWithSpecifiedEngine = array();
        foreach ($tablesEngines as $item) {
            if (empty($item['engine']) || $item['engine'] !== $engine) {
                $notWithSpecifiedEngine[] = $item;
            }
        }

        return empty($notWithSpecifiedEngine);
    }

    /*
    |--------------------------------------------------------------------------
    | Chatsets and collations
    |--------------------------------------------------------------------------
    */

    public function alterCharsetAndCollation($table, $charset, $collation)
    {
        $query = "ALTER TABLE $table CONVERT TO CHARACTER SET $charset, COLLATE $collation;";
        $res = Database::statement($query);
        if (!$res) {
            throw new \Exception("Query failed : $query");
        }
    }

    public function getCollations($database)
    {
        $query = '';
        $query .= 'SELECT table_name, table_collation ';
        $query .= 'FROM   information_schema.tables ';
        $query .= 'WHERE table_schema = ? ';

        return Database::select($query, array($database));
    }

    public function isCollation($database, $collation)
    {
        $tablesCollations = $this->getCollations($database);

        $notWithSpecifiedCollation = array();
        foreach ($tablesCollations as $item) {
            if (empty($item['table_collation']) || $item['table_collation'] !== $collation) {
                $notWithSpecifiedCollation[] = $item;
            }
        }

        return empty($notWithSpecifiedCollation);
    }

}