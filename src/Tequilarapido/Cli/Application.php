<?php namespace Tequilarapido\Cli;

use KevinGH\Amend\Command;
use KevinGH\Amend\Helper;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Application extends BaseApplication
{
    protected $appDispatcher;

    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        $this->appDispatcher = new EventDispatcher();
        $this->setDispatcher($this->appDispatcher);
    }

    public function getDispatcher()
    {
        return $this->appDispatcher;
    }

    public function addUpdateCommand($manifestURL)
    {
        $updateCommand = new Command('self-update');
        $updateCommand->setManifestUri($manifestURL);
        $this->getHelperSet()->set(new Helper());
        $this->add($updateCommand);
    }
}


