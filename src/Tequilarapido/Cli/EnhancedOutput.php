<?php namespace Tequilarapido\Cli;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class EnhancedOutput
 *
 * @implements Symfony\Component\Console\Output\OutputInterface
 * @package Teq\Components
 */
class EnhancedOutput extends ConsoleOutput
{

    public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null, OutputFormatterInterface $formatter = null)
    {
        parent::__construct($verbosity, $decorated, $formatter);

        $style = new OutputFormatterStyle('green');
        $this->getFormatter()->setStyle('success', $style);
    }

    public function title($title)
    {
        $this->writeln("");
        $this->writeln("");
        $this->writeln("-------------------------------------------------");
        $this->writeln(" $title");
        $this->writeln("-------------------------------------------------");
    }

    public function info($message = '', $space = false)
    {
        $message = $this->space($space) . ' ' . $message;
        $this->writeln("<info>$message</info>");
    }

    public function success($message = '', $space = false)
    {
        $message = $this->space($space) . ' ' . $message;
        $this->writeln("<success>$message</success>");
    }

    public function warn($message = '', $space = false)
    {
        $message = $this->space($space) . '[WARN] ' . $message;
        $this->writeln("<comment>$message</comment>");
    }

    public function error($message = '', $exitOnError = true, $space = false)
    {
        $message = $this->space($space) . '[ERROR] ' . $message;
        $this->writeln("<error>$message</error>");

        if ($exitOnError) {
            throw new \Exception('[ERROR] Execution aborted!');
        }
    }

    /**
     * @param boolean $space
     */
    private function space($space)
    {
        return $space ? "\n\n\n" : "";
    }

}