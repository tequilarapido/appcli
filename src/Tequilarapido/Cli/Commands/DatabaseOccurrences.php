<?php namespace Tequilarapido\Cli\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tequilarapido\Cli\Commands\Base\AbstractDatabaseCommand;
use Tequilarapido\Database\Column;
use Tequilarapido\Helpers\ShellHelper;
use Tequilarapido\PHPSerialized\SearchReplace;

class DatabaseOccurrences extends AbstractDatabaseCommand
{

    const ARGUMENT_SEARCH = 'search';
    const ARGUMENT_SEARCH_SEPARATOR = '|';

    protected $occurrences = array();

    protected function configure()
    {
        parent::configure();

        $description = '';
        $description .= 'Search string in database (even in serialized objects). ' . PHP_EOL;
        $description .= '  ';

        $this
            ->setName('db:occurrences')
            ->setDescription($description)
            ->addArgument(
                static::ARGUMENT_SEARCH,
                InputArgument::OPTIONAL,
                'What to search for ? if not specified, the search items are taken from
                config file like for db:replace command. If multiple items, separate them by |',
                null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // Search from argument or config ?
        if ($search = $input->getArgument(static::ARGUMENT_SEARCH)) {
            $replacements = $this->getReplacementsFromSearch($search);
        } else {
            $replacements = $this->config->getReplacements();
        }

        if (is_null($replacements)) {
            $this->output->warn('There is nothing to search according to configuration.');
            return;
        }

        // Setup connection
        $this->setup();

        // Text data columns
        $this->output->info('Analysing database : looking for text columns ...');
        $column = new Column();
        $excludeTables = $this->config->getExcludeTables();
        $textColumns = $column->scanTextColumns($this->databaseName, $excludeTables);

        // Start progress
        $progress = $this->getHelperSet()->get('progress');
        $progress->start($this->output, count($textColumns));

        // Process
        foreach ($textColumns as $tableName => $tableInfos) {
            $progress->advance();
            $this->output->writeln(" : Processing $tableName  ");
            $this->occurrences[$tableName] = $this->getOccurrences($tableName, $tableInfos, $replacements);
        }

        // End progress
        $progress->finish();

        // Display
        $this->displayOccurrences();
    }

    protected function getOccurrences($tableName, $tableInfos, $replacements)
    {
        $occurrences = array();

        // Is there columns ?
        if (empty($tableInfos['columns'])) {
            return;
        }

        // Iterates in each column and looks for the old string
        foreach ($tableInfos['columns'] as $field_name) {
            foreach ($replacements as $replacement) {
                $searchQuery = $this->searchQuery($tableName, $tableInfos, $field_name, $replacement);
                $search_results = $this->db->select($searchQuery);

                if (!isset($occurrences[$replacement->from])) {
                    $occurrences[$replacement->from] = 0;
                }
                $occurrences[$replacement->from] += count($search_results);
            }
        }

        // End shell progress
        ShellHelper::progressEnd($this->output);

        return $occurrences;
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

    protected function displayOccurrences()
    {

        $this->output->title('Occurrences by table');

        $total = 0;
        foreach ($this->occurrences as $tableName => $tableOccurrences) {

            // Table occurrence
            $tableTotal = 0;
            foreach ($tableOccurrences as $searchTotal) {
                $tableTotal += $searchTotal;
            }

            if ($tableTotal > 0) {
                $this->output->info($tableName . ' : ' . $tableTotal);
                $total += $tableTotal;
            }
        }

        // No occurrence ?
        if ($total == 0)
        {
            $this->output->info('None.');
        }

        // Recap
        $this->output->title('Total ');
        $this->output->success('Total occurrences : ' . $total);
    }

    private function getReplacementsFromSearch($search)
    {
        $items = explode(static::ARGUMENT_SEARCH_SEPARATOR, $search);
        $replacements = array();

        foreach ($items as $item) {
            if (!empty($item)) {
                $replacement = new \stdClass;
                $replacement->from = trim($item);

                $replacements[] = $replacement;
            }
        }

        return $replacements;
    }

}