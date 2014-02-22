<?php namespace Tequilarapido\Cli\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tequilarapido\Cli\Commands\Base\AbstractConfigurableCommand;

class Infos extends AbstractConfigurableCommand
{

    protected function configure()
    {
        parent::configure();

        $description = '';
        $description .= 'Information about package. ' . PHP_EOL;
        $description .= '  ';

        $this
            ->setName('infos')
            ->setDescription($description);


    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // Affiche le fichier de configuration
        $this->output->title('Configuration File : ' . $this->config->getFile());
        $this->output->info($this->config->getFormattedConfiguration());
    }

}