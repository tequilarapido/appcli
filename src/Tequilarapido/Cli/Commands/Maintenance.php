<?php namespace Tequilarapido\Cli\Commands;

use Datum\Datum;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Tequilarapido\Cli\Commands\Base\AbstractCommand;

class Maintenance extends AbstractCommand
{
    const ARGUMENT_MODE = 'mode';
    const OPTION_DONT_CHECK_DIRECTORY = 'dont-check-directory';
    const MODE_OFF = 'off';
    const MODE_ON = 'on';
    const MODE_STATUS = 'status';
    const MAINTENANCE_FILE = '.maintenance';

    protected $mode = '';

    protected function configure()
    {
        parent::configure();

        $description = '';
        $description .= 'Can be used to take put the application in maintenance mode and bring it live again. ' . PHP_EOL;
        $description .= 'It add a maintenance file on the application root. It is up to the application to use it. ' . PHP_EOL;

        $this
            ->setName('maintenance')
            ->setDescription($description)
            ->addArgument(
                self::ARGUMENT_MODE,
                InputArgument::OPTIONAL,
                'mode=off : remove maintenance mode or mode=off : put the application uin maintenance mode ',
                null
            )->addOption(
                static::OPTION_DONT_CHECK_DIRECTORY,
                null,
                InputOption::VALUE_NONE,
                'Will not check if we are on wordpress directory. Useful for running tests.'
            );


    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // Don't check directory if requested
        if (!$this->input->getOption(static::OPTION_DONT_CHECK_DIRECTORY)) {
            $this->checkExecutionDirectory();
        }

        // Parse mode and execute related command
        $this->parseMode($input->getArgument(self::ARGUMENT_MODE));
        if (static::MODE_ON === $this->mode) {
            $this->maintenanceOn();
        } elseif (static::MODE_OFF === $this->mode) {
            $this->maintenanceOff();
        } else {
            $this->maintenanceStatus();
        }
    }

    protected function maintenanceOn()
    {
        $fs = new Filesystem();
        $this->output->info('Maintenance mode is now on');
        $fs->touch($this->getMaintenanceFile());
    }

    protected function maintenanceOff()
    {
        $fs = new Filesystem();
        $fs->remove($this->getMaintenanceFile());
        $this->output->info('Maintenance mode is now off');
    }

    protected function maintenanceStatus()
    {
        $fs = new Filesystem();
        $currentMode = $fs->exists($this->getMaintenanceFile()) ? static::MODE_ON : static::MODE_OFF;

        // Since ?
        $since = '';
        if ($currentMode == static::MODE_ON) {
            $since = Datum::createFromTimestamp(filemtime($this->getMaintenanceFile()))->diffForHumans();
            $since = " (activated $since)";
        }

        $this->output->info("Current Maintenance mode : $currentMode $since");
    }

    protected function checkExecutionDirectory()
    {
        $fs = new Filesystem();
        $cwd = $this->getProjectDirectory();

        // Is it wordpress dir ?
        $wordpressDir = $fs->exists(array($cwd . 'wp-admin', $cwd . 'wp-includes'));

        // Is it appcli project dir (pass for test and dev) ?
        $wpcliprojectDir = $fs->exists(array($cwd . 'bin', $cwd . 'src/Teq/Tasks/Command/Maintenance.php', $cwd . 'box.json.dist'));


        if (!$wordpressDir && !$wpcliprojectDir) {
            $this->output->error("You must be on the Doc root (wordpress directory : we cannot find wp-admin or wp-includes directory for instance.)");
        }

    }

    protected function parseMode($mode)
    {
        if (is_null($mode)) {
            $mode = static::MODE_STATUS;
        }

        if (in_array(strtolower($mode), array(static::MODE_ON, static::MODE_OFF, static::MODE_STATUS))) {
            $this->mode = $mode;
        } else {
            $this->output->error('Unknown mode.');
        }
    }

    protected function getProjectDirectory()
    {
        return getcwd() . '/';
    }

    protected function getMaintenanceFile()
    {
        return $this->getProjectDirectory() . static::MAINTENANCE_FILE;
    }

}