<?php namespace Tequilarapido\Cli\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tequilarapido\Cli\Commands\Base\AbstractDatabaseCommand;
use Tequilarapido\Database\Database;
use Tequilarapido\Database\Table;

class DatabaseConvertToUtf8 extends AbstractDatabaseCommand
{
    const CHARSET = 'utf8';
    const COLLATION = 'utf8_general_ci';

    protected $databaseTables;

    protected function configure()
    {
        parent::configure();

        $description = '';
        $description .= 'Converts database tables to utf8. ' . PHP_EOL;
        $description .= '  ';

        $this
            ->setName('db:utf8')
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
        $this->setCharsetAndCollation(static::CHARSET, static::COLLATION);

        // Test out
        $isOK = $this->table->isCollation($databaseName, static::COLLATION);
        if ($isOK) {
            $this->output->success("Done. All table are now " . static::CHARSET . '/' . static::COLLATION);
        } else {
            $this->output->warn("Done. But not sure all tables are " . static::CHARSET . '/' . static::COLLATION);
        }
    }

    protected function setCharsetAndCollation($charset, $collation)
    {
        $this->output->title("Setting database charset to $charset and collation to $collation");

        // Cli progress setup
        $progress = $this->getHelperSet()->get('progress');
        $progress->start($this->output, count($this->databaseTables));

        foreach ($this->databaseTables as $tableName) {

            $this->output->info($tableName);
            $this->table->alterCharsetAndCollation($tableName, $charset, $collation);
            $progress->advance();
        }
        $progress->finish();
    }

}