<?php namespace Tequilarapido\Cli\Commands\Base;

use Datum\Datum;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    protected $startTime;
    protected $progress;

    /**
     * @var InputInterface
     */
    protected $input;


    /**
     * @var OutputInterface
     * We need it public, as it is used inside a closure in AbstractCommand::elapsed method
     */
    public $output;


    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Output
        $this->output = $output;
        $this->input = $input;
    }

    public function table($data)
    {
        $table = $this->getHelperSet()->get('table');
        $table->setHeaders(array('Property', 'Value'));
        foreach ($data as $key => $value) {
            $table->addRow(array($key, $value));
        }

        $table->render($this->output);
    }


    //
    // Time / Benchmarking stuff
    //
    protected function elapsed()
    {
        // Start
        $this->startTime = time();
        $self = $this;

        try {
            $this->getApplication()->getDispatcher()->addListener(ConsoleEvents::TERMINATE, function () use ($self) {
                $dt = Datum::createFromTimestamp($self->getStartTime());
                $elapsed = time() - $self->getStartTime();

                $self->output->info('');
                $self->output->info('Took about ' . $dt->diffInMinutes() . ' min. ( ' . number_format($elapsed) . ' sec.) ');
            });
        } catch (\Exception $e) {
            // Do nothing.
        }
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    protected function iAmHungry($memory_limit = '1024M')
    {
        ini_set('memory_limit', $memory_limit);
        set_time_limit(0);
    }


}