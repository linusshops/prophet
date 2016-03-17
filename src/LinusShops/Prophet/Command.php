<?php
/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-03-16
 */

namespace LinusShops\Prophet;

use Symfony\Component\Process\Process;

class Command extends \Symfony\Component\Console\Command\Command
{
    protected function shell($command, $workingDirectory = '.')
    {
        $process = new Process($command, $workingDirectory, null, null, null);
        return $process->run(function ($type, $buffer) {
            echo $buffer;
        }) == 0;
    }
}
