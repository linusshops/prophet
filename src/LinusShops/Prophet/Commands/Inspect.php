<?php
/**
     *
     *
     * @author Sam Schmidt <samuel@dersam.net>
     * @date 2015-08-03
     * @company Linus Shops
     */

namespace LinusShops\Prophet\Commands;

use LinusShops\Prophet\Magento;
use LinusShops\Prophet\ProphetCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class Inspect extends ProphetCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('inspect')
            ->setDescription('Bootstrap magento and activate Psysh');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $setupSuccessful = parent::execute($input, $output);

        if ($setupSuccessful) {
            Magento::bootstrap();
            \PD::inspect();
        }
    }
}
