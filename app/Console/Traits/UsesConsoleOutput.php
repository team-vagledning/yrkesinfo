<?php

namespace App\Console\Traits;

use Symfony\Component\Console\Style\OutputStyle;

trait UsesConsoleOutput
{
    /**
     * @var OutputStyle
     */
    protected $consoleOutput;

    /**
     * @param OutputStyle $consoleOutput
     * @return $this
     */
    public function setConsoleOutput(OutputStyle $consoleOutput)
    {
        $this->consoleOutput = $consoleOutput;
        return $this;
    }
}
