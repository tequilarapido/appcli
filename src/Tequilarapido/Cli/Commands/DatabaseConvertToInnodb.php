<?php namespace Tequilarapido\Cli\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tequilarapido\Cli\Commands\Base\AbstractDatabaseCommand;
use Tequilarapido\Cli\Commands\Base\AbstractDatabaseConvertToEngine;
use Tequilarapido\Database\Database;
use Tequilarapido\Database\Table;

class DatabaseConvertToInnodb extends AbstractDatabaseConvertToEngine
{
    public function __construct($name = null)
    {
        $this->engine = 'InnoDB';
        parent::__construct($name);
    }
}
