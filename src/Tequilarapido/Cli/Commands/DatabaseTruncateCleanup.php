<?php namespace Tequilarapido\Cli\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tequilarapido\Cli\Commands\Base\AbstractDatabaseCommand;
use Tequilarapido\Database\Table;

class DatabaseTruncateCleanup extends AbstractDatabaseCommand
{

    protected function configure()
    {
        parent::configure();

        $description = '';
        $description .= 'Clean up database by truncating tables specified in configuration file. ' . PHP_EOL;
        $description .= '  ';

        $this
            ->setName('db:truncate')
            ->setDescription($description);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // Operations config
        $cleanupConf = $this->config->getTruncateCleanup();
        if (is_null($cleanupConf)) {
            $this->output->warn('There is nothing to truncate according to configuration.');
            return;
        }

        // Setup connection
        $this->setup();

        // Tables
        $tables = $this->filterByConf($cleanupConf);

        // Perform
        $this->databaseSize['before'] = $this->db->size($this->config->getDatabaseName());
        $this->performTruncateOperations($tables);
        $this->databaseSize['after'] = $this->db->size($this->config->getDatabaseName());

        // Output gain
        $this->outputGain();
    }

    protected function filterByConf($cleanupConf)
    {
        $tables = array();

        // Get all tables
        $this->table = new Table();
        $all = $this->table->all($this->databaseName);

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
                $matchedTables = $this->table->getTablesForMulti($t, $this->databasePrefix, $all);
                if (!empty($matchedTables)) {
                    $tables = array_merge($tables, $matchedTables);
                }
            }
        }

        return array_unique($tables);
    }

    protected function performTruncateOperations($tables)
    {
        foreach ($tables as $table) {
            $this->output->info("Truncating $table ...");
            foreach ($tables as $table) {
                $this->db->table($table)->truncate();
            }
        }
    }

}