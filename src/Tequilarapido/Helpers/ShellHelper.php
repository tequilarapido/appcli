<?php namespace Tequilarapido\Helpers;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ShellHelper
{
    const CHAR_SAMELINE = "\x0D";

    /**
     * Progress cursors
     * @var array
     */
    static protected $cursors = array('-', '\\', '|', '/');

    /**
     * @param $cmd
     * @return Process
     */
    public static function run($cmd)
    {
        if (static::isCygwin()) {
            $cmd = "C:\cygwin\bin\bash.exe --login  -c '" . $cmd . "'";
        }

        $process = new Process($cmd);
        $process->run();
        return $process;
    }

    public static function isCygwin()
    {
        $cmd = "cygpath -w ~";
        $process = new Process($cmd);
        $process->run();

        return $process->isSuccessful();
    }

    public static function progress(OutputInterface $output)
    {
        $char = current(static::$cursors);
        $output->write(static::CHAR_SAMELINE . $char);

        if (false === next(static::$cursors)) {
            reset(static::$cursors);
        }
    }

    public static function progressEnd(OutputInterface $output)
    {
        $output->write(static::CHAR_SAMELINE . ' ');
    }

}