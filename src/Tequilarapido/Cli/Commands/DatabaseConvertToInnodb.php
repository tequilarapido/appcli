<?php namespace Tequilarapido\Cli\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tequilarapido\Cli\Commands\Base\AbstractDatabaseCommand;
use Tequilarapido\Database\Database;
use Tequilarapido\Database\Table;

class DatabaseConvertToInnodb extends AbstractDatabaseCommand
{
    const ENGINE = 'InnoDB';

    protected $databaseTables;

    protected function configure()
    {
        parent::configure();

        $description = '';
        $description .= 'Converts database tables to innodb. ' . PHP_EOL;
        $description .= '  ';

        $this
            ->setName('db:innodb')
            ->setDescription($description);


    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // Database configuration
        $dbConfig = $this->config->getDatabase();
        $databaseName = $this->config->getDatabaseName();

        // Connection
        $this->db = new Database();
        $this->db->connect($dbConfig);

        // Tables
        $this->table = new Table();
        $this->databaseTables = $this->table->all($databaseName);

        // Set proper engine
        $this->setEngine(static::ENGINE);

        // Test out
        $isOK = $this->table->isEngine($databaseName, static::ENGINE);
        if ($isOK) {
            $this->output->success("Done. All tables are now " . static::ENGINE);
        } else {
            $this->output->warn("Done. But not sure all tables are  " . static::ENGINE);
        }
    }

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