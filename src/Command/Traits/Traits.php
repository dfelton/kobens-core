<?php

namespace Kobens\Core\Command\Traits;

use Symfony\Component\Console\Output\OutputInterface;

trait Traits
{
    protected function sleep(OutputInterface $output = null, int $seconds = 5) : void
    {
        if ($seconds <= 0) {
            return;
        }
        for ($i = $seconds; $i > 0; $i--) {
            if ($output && !$output->isQuiet()) {
                $output->write('.');
            }
           \sleep(1);
        }
    }

    protected function getNow() : string
    {
        return (new \DateTime())->format('Y-m-d H:i:s');
    }

    protected function clearTerminal(OutputInterface $output) : void
    {
        if (!$output->isQuiet()) {
            $output->write(chr(27).chr(91).'H'.chr(27).chr(91).'J');
        }
    }
}
