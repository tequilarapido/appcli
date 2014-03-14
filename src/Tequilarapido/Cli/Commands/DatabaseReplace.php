<?php namespace Tequilarapido\Cli\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tequilarapido\Cli\Commands\Base\AbstractDatabaseCommand;
use Tequilarapido\Database\Column;
use Tequilarapido\Helpers\ShellHelper;
use Tequilarapido\PHPSerialized\SearchReplace;

class DatabaseReplace extends AbstractDatabaseCommand
{
    const OPTION_USE_TRANSACTIONS = 'use-transactions';

    protected $useTransactions = false;

    protected function configure()
    {
        parent::configure();

        $description = '';
        $description .= 'Search and replace string in database (even in serialized objects). ' . PHP_EOL;
        $description .= 'Can be used to switch domain for a WordPress application, when moving to different environement. ' . PHP_EOL;
        $description .= '  ';

        $this
            ->setName('db:replace')
            ->setDescription($description)
            ->addOption(
                static::OPTION_USE_TRANSACTIONS,
                null,
                InputOption::VALUE_NONE,
                'If specified, SQL update operations will be grouped into transactions.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->useTransactions = $input->getOption(static::OPTION_USE_TRANSACTIONS);
        $replacements = $this->config->getReplacements();
        $excludeTables = $this->config->getExcludeTables();

        // Operations config
        if (is_null($replacements)) {
            $this->output->warn('There is nothing to replace according to configuration.');
            return;
        }

        // Setup connection
        $this->setup();

        // Process
        $textColumns = $this->analyseDatabase($excludeTables);
        $progress = $this->startProgress(count($textColumns));
        $queriesCount = $this->processDatabase($textColumns, $progress, $replacements);
        $progress->finish();

        if ($this->useTransactions) {
            $this->output->info("Queries were executed using transactions.\n");
        }
        $this->output->info("Total executed queries : $queriesCount");

        // Mail
        $this->notify();
    }

    protected function startProgress($total)
    {
        $progress = $this->getHelperSet()->get('progress');
        $progress->start($this->output, $total);
        return $progress;
    }

    protected function notify()
    {
        $mail = array(
            'subject' => 'Done.',
            'body' => ''
        );

        parent::notify($mail);
    }

    /**
     * @param $excludeTables
     * @return array
     */
    protected function analyseDatabase($excludeTables)
    {
        $this->output->info('Analysing database : looking for text columns ...');
        $column = new Column();
        $textColumns = $column->scanTextColumns($this->databaseName, $excludeTables);
        return $textColumns;
    }

    protected function setup()
    {
        parent::setup();

        // Need memory on big databases
        if ($this->useTransactions) {
            $this->iAmHungry();
        }
    }

    /**
     * @param $textColumns
     * @param $progress
     * @param $replacements
     * @return int
     */
    protected function processDatabase($textColumns, $progress, $replacements)
    {
        $queriesCount = 0;
        foreach ($textColumns as $tableName => $tableInfos) {
            $progress->advance();
            $this->output->writeln(" : Processing $tableName  ");
            $tableCount = $this->processTable($tableName, $tableInfos, $replacements);
            $queriesCount += $tableCount;
            $this->output->writeln("                                        -> $tableCount queries ");
        }
        return $queriesCount;
    }

    protected function processTable($tableName, $tableInfos, $replacements)
    {
        $queriesCount = 0;

        // Is there columns ?
        if (empty($tableInfos['columns'])) {
            return;
        }

        // Iterates in each column and looks for the old string
        foreach ($tableInfos['columns'] as $field_name) {

            $this->beginTransaction();

            foreach ($replacements as $replacement) {

                // Search
                $searchQuery = $this->searchQuery($tableName, $tableInfos, $field_name, $replacement);
                $search_results = $this->db->select($searchQuery);
                if (empty($search_results)) {
                    continue;
                }

                // Loop through result and  search/replace
                foreach ($search_results as $found_data) {

                    // Pk check
                    if (isset($found_data['_id'])) {
                        $id = $found_data['_id'];
                        unset($found_data['_id']);
                    }
                    $found_data = current($found_data);

                    // Try to replace string
                    try {
                        $sr = new SearchReplace;
                        $edited_data = $sr->run($replacement->from, $replacement->to, $found_data);
                    } catch
                    (\Exception $e) {
                        continue;
                    }

                    // Update entry / value
                    if (isset($id)) {
                        $this->db
                            ->table($tableName)
                            ->where($tableInfos['pk'], '=', $id)
                            ->update(array($field_name => $edited_data));

                    } else {
                        $this->db
                            ->table($tableName)
                            ->where($field_name, '=', $found_data)
                            ->update(array($field_name => $edited_data));
                    }

                    $queriesCount++;

                    // display progress
                    ShellHelper::progress($this->output);
                }
            }

            // Commit updates if using transactions
            $this->endTransaction();
        }

        // End shell progress
        ShellHelper::progressEnd($this->output);
        return $queriesCount;
    }

    protected function searchQuery($tableName, $tableInfos, $field_name, $replacement)
    {
        // table with primary key ?
        if (empty($tableInfos['pk'])) {
            return sprintf('SELECT `%s` FROM %s WHERE `%s` LIKE "%%%s%%"', $field_name, $tableName, $field_name, $replacement->from);
        } else {
            return sprintf('SELECT `%s`, ' . $tableInfos['pk'] . ' AS _id FROM %s WHERE `%s` LIKE "%%%s%%"', $field_name, $tableName, $field_name, $replacement->from);
        }
    }

    private function beginTransaction()
    {
        if ($this->useTransactions) {
            $this->db->beginTransaction();
        }
    }

    private function endTransaction()
    {
        if ($this->useTransactions) {
            $this->db->commit();
        }
    }

}