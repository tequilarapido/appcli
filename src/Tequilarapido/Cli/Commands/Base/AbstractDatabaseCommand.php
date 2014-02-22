<?php namespace Tequilarapido\Cli\Commands\Base;


use Tequilarapido\Database\Database;

abstract class  AbstractDatabaseCommand extends AbstractConfigurableCommand
{

    protected $databaseName;
    protected $databasePrefix;

    /**
     * Database size (before and after) used in outputGain
     * @var array
     */
    protected $databaseSize = array();

    /**
     * @var Database
     */
    protected $db;

    /**
     * @var Table
     */
    protected $table;

    protected function setup()
    {
        // Database name
        $this->databaseName = $this->config->getDatabaseName();

        // Database tables prefix
        $this->databasePrefix = $this->config->getDatabasePrefix();

        // Golbal connection
        $this->db = new Database();
        $dbConfig = $this->config->getDatabase();
        $this->db->connect($dbConfig);
    }

    protected function outputGain()
    {
        $gain = $this->databaseSize['before'] - $this->databaseSize['after'];
        $percentGain = round(($gain * 100) / $this->databaseSize['before'], 2);
        $this->output->info();
        $this->output->info("Database size: before=" . $this->databaseSize['before'] . "Mb -> after=" . $this->databaseSize['after'] . "Mb > Gain : " . $gain . "Mb (" . $percentGain . "%)");
    }

}