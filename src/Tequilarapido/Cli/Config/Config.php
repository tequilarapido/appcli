<?php namespace Tequilarapido\Cli\Config;

use Hazbo\Json\Formatter;
use Herrera\Json\Exception\JsonException;
use Herrera\Json\Json;

/**
 * Manage configuration from the json configuration file
 */
class Config
{

    protected $file;
    protected $schemaFile;
    protected $raw;

    public function __construct($file, $schemaFile)
    {
        $this->file = $file;
        $this->schemaFile = $schemaFile;

        $this->load();
    }

    protected function load()
    {
        $linter = new Json();
        $json = $linter->decodeFile($this->file);

        // Validate json against schema
        try {
            $linter->validate($linter->decodeFile($this->schemaFile), $json);
        } catch (JsonException $e) {
            $this->echoJsonException($e);
        }

        $this->raw = $json;
    }

    public function getRaw()
    {
        return $this->raw;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getFormattedConfiguration()
    {
        $formatter = new Formatter();
        return $formatter->format(json_encode($this->raw));
    }

    public function getProject()
    {
        if (isset($this->raw->project)) {
            return $this->raw->project;
        }

        return null;
    }


    public function getReplacements()
    {
        if (isset($this->raw->replace->replacements)) {
            return $this->raw->replace->replacements;
        }

        return null;
    }

    public function isLockTables()
    {
        if (isset($this->raw->replace->lockTables)) {
            return (bool)json_decode($this->raw->replace->lockTables);
        }

        return null;
    }

    public function getExcludeTables()
    {
        if (isset($this->raw->replace->excludeTables) && is_array($this->raw->replace->excludeTables)) {
            return $this->raw->replace->excludeTables;
        }

        return array();
    }


    public function isSingleWebSite()
    {
        $replacements = $this->getReplacements();
        return is_array($replacements) && count($replacements) === 1;
    }

    public function getDatabase()
    {
        if (isset($this->raw->database)) {
            return array(
                'driver' => 'mysql',
                'host' => $this->raw->database->host,
                'database' => $this->raw->database->database,
                'username' => $this->raw->database->username,
                'password' => $this->raw->database->password,
                'charset' => 'utf8',
                'collation' => 'utf8_general_ci',
                'prefix' => '',
            );
        }

        return null;
    }

    public function getDatabaseName()
    {
        if (!empty($this->raw->database->database)) {
            return $this->raw->database->database;
        }

        throw new \LogicException('Database name is not specified. Maybe the configuration file is not valid');
    }

    public function getDatabasePrefix()
    {
        if (!empty($this->raw->database->prefix)) {
            return $this->raw->database->prefix;
        }

        throw new \LogicException('Database prefix is not specified. Maybe the configuration file is not valid');
    }

    public function getCleanup()
    {
        if (isset($this->raw->cleanup)) {
            return $this->raw->cleanup;
        }

        return null;
    }

    public function getTruncateCleanup()
    {
        $conf = new \stdClass();

        if (isset($this->raw->cleanup->truncate->simple)) {
            $conf->simple = (array)$this->raw->cleanup->truncate->simple;
        }

        if (isset($this->raw->cleanup->truncate->multi)) {
            $conf->multi = (array)$this->raw->cleanup->truncate->multi;
        }

        if (!isset($conf->simple) && !isset($conf->multi)) {
            return null;
        }

        return $conf;
    }

    public function getDeleteCleanup()
    {
        if (empty($this->raw->cleanup->delete)) {
            return null;
        }

        return $this->raw->cleanup->delete;
    }

    public function getTablesToTruncate()
    {
        $tuncateConf = $this->getTruncateCleanup();
        if (is_null($tuncateConf)) {
            return null;
        }

        // Tables
        $tables = array();
        if (!empty($tuncateConf->simple) && is_array($tuncateConf->simple)) {
            $tables = array_merge($tables, $tuncateConf->simple);
        }

        if (!empty($tuncateConf->multi) && is_array($tuncateConf->multi)) {
            $multiTables = $this->getRealTables($tuncateConf->multi);
            $tables = array_merge($tables, $multiTables);
        }

        return null;
    }

    /**
     * @param string $command
     */
    public function isNotifyOnForCommand($command)
    {
        return !empty($this->raw->{$command}->notify)
            && $this->raw->{$command}->notify;
    }

    public function getNotificationConfig()
    {
        return !empty($this->raw->notify)
            && $this->raw->notify;
    }

    public function getNotificationTransport()
    {
        return empty($this->raw->notify->transport) ? null : $this->raw->notify->transport;
    }

    public function getNotificationFrom()
    {
        return empty($this->raw->notify->from) ? null : $this->raw->notify->from;
    }

    public function getNotificationTo()
    {
        return empty($this->raw->notify->to) ? null : $this->raw->notify->to;
    }


    /**
     * @param JsonException $e
     */
    private function echoJsonException($e)
    {
        echo 'Your configuration file may contain errors, ' . $e->getMessage() . PHP_EOL;
        foreach ($e->getErrors() as $k => $error) {
            echo ($k + 1) . ' : ' . $error . PHP_EOL;
        }
    }

    private function getRealTables($genericTables)
    {
        $prefix = $this->getDatabasePrefix();
        $tables = array();
        foreach ($genericTables as $t) {
            $pattern = '/^' . $prefix . '([0-9]+_)?' . $t . '$/';
            $tables[] = preg_grep($pattern, $this->databaseTables);
        }

    }

}