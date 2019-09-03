<?php

namespace App\Console\Traits;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\OutputStyle;

trait HasProgressBar
{
    /**
     * @var ProgressBar
     */
    protected $bar;

    /**
     * @param $count
     * @param string $processing
     * @throws \Exception
     */
    public function initializeProgressBar($count, $processing = self::class)
    {
        if (property_exists($this, 'consoleOutput') === false) {
            throw new \Exception('Class must have consoleOutput property');
        }

        if ($this->consoleOutput instanceof OutputStyle === false) {
            throw new \Exception('consoleOutput must be of type OutputStyle, set via setConsoleOutput');
        }

        $this->bar = $this->consoleOutput->createProgressBar($count);
        $this->bar->setFormat(
            implode(
                "\n",
                [
                    "Processing Script : $processing",
                    "         Progress : [%bar%] %percent:3s%%  (%current%/%max%  )",
                    "      Last action : %action%"
                ]
            )
        );
    }

    /**
     * @param string $action
     */
    public function advanceProgressBar($action = "")
    {
        $this->bar->setMessage($action, 'action');
        $this->bar->advance();
    }

    /**
     * Finishes the progress bar
     */
    public function finishProgressBar()
    {
        $this->bar->finish();
    }
}
