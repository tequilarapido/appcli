<?php namespace Tequilarapido\Cli\Commands\Base;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tequilarapido\Cli\Commands\Base\AbstractDatabaseCommand;
use Tequilarapido\Database\Database;
use Tequilarapido\Database\Table;

abstract class AbstractDatabaseConvertToEngine extends AbstractDatabaseCommand
{
    protected $engine = null;

    protected $databaseTables;

    protected function configure()
    {
        parent::configure();

        $description = '';
        $description .= 'Converts database tables to ' . $this->engine . '. ' . PHP_EOL;
        $description .= '  ';

        $this
            ->setName('db:' . strtolower($this->engine))
            ->setDescription($description);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // Database configuration
        $dbConfig     = $this->config->getDatabase();
        $databaseName = $this->config->getDatabaseName();

        // Connection
        $this->db = new Database();
        $this->db->connect($dbConfig);

        // Tables
        $this->table          = new Table();
        $this->databaseTables = $this->table->all($databaseName);

        // Set proper engine
        $this->setEngine($this->engine);

        // Test out
        $isOK = $this->table->isEngine($databaseName, $this->engine);
        if ($isOK) {
            $this->output->success("Done. All tables are now " . $this->engine);
        } else {
            $this->output->warn("Done. But not sure all tables are  " . $this->engine);
        }
    }

    /**
     * @param string $engine
     */
    protected function setEngine($engine)
    {
        $this->output->title("Setting database engine to $engine");

        // Cli progress setup
        $progress = $this->getHelperSet()->get('progress');
        $progress->start($this->output, count($this->databaseTables));

        foreach ($this->databaseTables as $tableName) {

            $this->output->info($tableName);
            $this->table->alterEngine($tableName, $engine);
            $progress->advance();
        }
        $progress->finish();
    }

}