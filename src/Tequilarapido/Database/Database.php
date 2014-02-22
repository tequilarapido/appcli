<?php namespace Tequilarapido\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{

    /**
     * Initialize Illuminate Database connection
     *
     * @param $dbConfig
     */
    public function connect($dbConfig)
    {
        $capsule = new Capsule;
        $capsule->addConnection($dbConfig);
        $capsule->bootEloquent();
        $capsule->setAsGlobal();
    }


    public function size($database)
    {
        $query = '';
        $query .= 'SELECT table_schema, Sum(data_length + index_length) as size ';
        $query .= 'FROM   information_schema.tables ';
        $query .= 'WHERE table_schema = ? ';
        $query .= 'GROUP BY table_schema';

        $res = static::select($query, array($database));
        if (!empty($res[0]['size'])) {
            return round($res[0]['size'] / 1024 / 1024);
        }

        return false;
    }

    //
    // Helpers
    //

    public static function select($query, $bindings = array())
    {
        return Capsule::connection()->select($query, $bindings);
    }

    public static function update($query, $bindings = array())
    {
        return Capsule::connection()->update($query, $bindings);
    }

    public static function statement($query, $bindings = array())
    {
        return Capsule::connection()->statement($query, $bindings);
    }


    public static function connection()
    {
        return Capsule::connection();
    }

    public static function schema()
    {
        return Capsule::schema();
    }

    public static function table($table)
    {
        return Capsule::table($table);
    }

}