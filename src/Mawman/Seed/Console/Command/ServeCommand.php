<?php


namespace Mawman\Seed\Console\Command;


use Mawman\Seed\Environment;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServeCommand extends Command
{
    protected function configure() {
        $this->setName("serve")
            ->setDescription("Start the development server");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $host = "localhost";
        $port = 3030;
        $cmd = "php -S {$host}:{$port} -t public/ public/index.php";
        chdir(Environment::getInstance()->getPath());
        passthru($cmd);
    }


}