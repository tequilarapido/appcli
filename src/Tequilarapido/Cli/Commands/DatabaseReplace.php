<?php namespace Tequilarapido\Cli\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tequilarapido\Cli\Commands\Base\AbstractDatabaseCommand;
use Tequilarapido\Database\Column;
use Tequilarapido\Helpers\ShellHelper;
use Tequilarapido\PHPSerialized\SearchReplace;

class DatabaseReplace extends AbstractDatabaseCommand
{

    protected function configure()
    {
        parent::configure();

        $description = '';
        $description .= 'Search and replace string in database (even in serialized objects). ' . PHP_EOL;
        $description .= 'Can be used to switch domain for a WordPress application, when moving to different environement. ' . PHP_EOL;
        $description .= '  ';

        $this
            ->setName('db:replace')
            ->setDescription($description);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // Operations config
        $replacements = $this->config->getReplacements();
        $excludeTables = $this->config->getExcludeTables();
        if (is_null($replacements)) {
            $this->output->warn('There is nothing to replace according to configuration.');
            return;
        }

        // Setup connection
        $this->setup();

        // Text data columns
        $this->output->info('Analysing database : looking for text columns ...');
        $column = new Column();
        $textColumns = $column->scanTextColumns($this->databaseName, $excludeTables);

        // Start progress
        $progress = $this->getHelperSet()->get('progress');
        $progress->start($this->output, count($textColumns));

        // Process
        $queriesCount = 0;
        $allQueries = array();
        foreach ($textColumns as $tableName => $tableInfos) {
            $progress->advance();
            $this->output->writeln(" : Processing $tableName  ");
            $queries = $this->processTable($tableName, $tableInfos, $replacements);
            $count = count($queries);
            $allQueries = array_merge($allQueries, $queries);
            $queriesCount += $count;
            $this->output->writeln("                                      Finished $tableName ($count queries) ");
        }
        $this->output->info("Total executed queries : $queriesCount");

        // End progress
        $progress->finish();

        // Notification
        $mail = array(
            'subject' => 'Done.',
            'body' => ''
        );

        $this->notify($mail);
    }

    protected function processTable($tableName, $tableInfos, $replacements)
    {
        $db_queries = array();

        // Is there columns ?
        if (empty($tableInfos['columns'])) {
            return;
        }

        // Iterates in each column and looks for the old string
        foreach ($tableInfos['columns'] as $field_name) {
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

                    $db_queries[] = $this->db->getLastQuery();

                    // display progress
                    ShellHelper::progress($this->output);
                }
            }
        }

        // End shell progress
        ShellHelper::progressEnd($this->output);
        return $db_queries;
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

}