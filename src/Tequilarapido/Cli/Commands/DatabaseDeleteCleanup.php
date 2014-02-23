<?php namespace Tequilarapido\Cli\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tequilarapido\Cli\Commands\Base\AbstractDatabaseCommand;
use Tequilarapido\Database\Database;
use Tequilarapido\Database\Table;

class DatabaseDeleteCleanup extends AbstractDatabaseCommand
{

    protected function configure()
    {
        parent::configure();

        $description = '';
        $description .= 'Clean up database by deleting records from tables specified in configuration file. ' . PHP_EOL;
        $description .= '  ';

        $this
            ->setName('db:delete')
            ->setDescription($description);


    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // Operations config
        $cleanupConf = $this->config->getDeleteCleanup();
        if (is_null($cleanupConf)) {
            $this->output->warn('There is nothing to delete according to configuration.');
            return;
        }

        // Setup connection
        $this->setup();

        // Perform
        $this->databaseSize['before'] = $this->db->size($this->config->getDatabaseName());
        $this->performDeleteOperations($cleanupConf);
        $this->databaseSize['after'] = $this->db->size($this->config->getDatabaseName());

        // Output gain
        $this->outputGain();
    }

    protected function performDeleteOperations($cleanupConf)
    {
        // Get all tables
        $this->table = new Table();
        $all = $this->table->all($this->databaseName);

        foreach ($cleanupConf as $tableOperations) {
            $this->performTableDeleteOperations($tableOperations, $all);
        }
    }

    protected function performTableDeleteOperations($tableOperations, $all)
    {
        // First : Let's get tables
        $tables = array();
        $table = $tableOperations->table;
        if (!$tableOperations->multi) {
            $tables[] = $table;
        } else {
            $matchedTables = $this->table->getTablesForMulti($table, $this->databasePrefix, $all);
            if (!empty($matchedTables)) {
                $tables = array_merge($tables, $matchedTables);
            }
        }

        // Perform delete operations
        foreach ($tables as $table) {
            $this->output->info("Deleting items from table $table ...");
            $this->output->info("     -> " . json_encode($tableOperations->conditions));
            $dbTable = Database::table($table);
            foreach ($tableOperations->conditions as $cond) {
                $dbTable->where($cond->field, $cond->operator, $cond->value);
            }
            $dbTable->delete();
        }
    }
}