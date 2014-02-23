<?php namespace Tequilarapido\Cli\Commands\Base;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tequilarapido\Cli\Commands\Base\AbstractCommand;
use Tequilarapido\Cli\Config\Config;
use Tequilarapido\Helpers\MailHelper;

/**
 *
 * All commands that inherit from this class, will take json configuration
 * file as first argument. And configuration will be accessible from $config variable.
 *
 * Class AbstractConfigurableCommand
 * @package Tequilarapido\Cli\Commands
 */
abstract class AbstractConfigurableCommand extends AbstractCommand
{
    /**
     * @var \Tequilarapido\Cli\Config\Config
     */
    protected $config = null;

    public function __construct($name = null)
    {
        // Check that we have a schema file defined
        if (!defined('CLI_SCHEMA_FILE')) {
            throw new \LogicException('You must define a CLI_SCHEMA_FILE constant');
        }

        parent::__construct($name);
    }


    /**
     * All commands take as first argument path to conf file
     */
    protected function configure()
    {
        $this->addArgument(
            'config-file',
            InputArgument::REQUIRED,
            'Configuration file path',
            null
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // Elapsed time ?
        $this->elapsed();

        // Config file
        $configFile = $this->input->getArgument('config-file');
        $cliFile = $this->getFileFullPath($configFile);
        if (!$cliFile) {
            throw new \LogicException("Cannot find config file. (Given : [$configFile])");
        }

        // Load file
        $this->config = new Config($configFile, CLI_SCHEMA_FILE);
    }


    protected function getFileFullPath($file)
    {
        // Let's try with nothing : full path ?
        if (is_file($file)) {
            return $file;
        }

        // Let's try current directory
        $cd = getcwd();
        $fullpath = $cd . '/' . $file;
        if ($cd && is_file($fullpath)) {
            return $fullpath;
        }

        // Check if it's writable
        if (!is_writable($fullpath)) {
            throw new \LogicException("Sorry but [$fullpath] is not writable!");
        }

        return false;
    }

    protected function notify($mail, $commandName = '')
    {
        // CommandName
        $commandName = !empty($commandName) ? $commandName : $this->getName();


        // Cannot notify ?
        if (!$this->config->isNotifyOnForCommand('replace')) {
            $this->output->info("No notifications sent.");
            return;
        }

        // Notifications are On
        $notificationConfig = $this->config->getNotificationConfig();
        if (empty($notificationConfig)) {
            $this->output->warn("Notify is on, but notify configuration are missing.");
            return;
        }

        // Send mail
        $mailHelper = new MailHelper($this->config);
        $mailHelper->send($mail, $commandName);
        $this->output->info("Notification sent.");
    }


}