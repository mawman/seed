<?php


namespace Mawman\Seed\Console\Command;


use Mawman\Seed\Environment;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServeCommand extends Command
{
    protected function configure() {
        $this->setName("serve")
            ->setDescription("Start the development server")
            ->addOption("host", "H", InputOption::VALUE_REQUIRED, "Listen on host or ip", "localhost")
            ->addOption("port", "P", InputOption::VALUE_REQUIRED, "Listen on port", 3030)
            ->addOption("all", "a", InputOption::VALUE_NONE, "Listen on all interfaces");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $host = $input->getOption("host");
        $port = $input->getOption("port");
        if ($input->getOption("all")) {
            $host = "0.0.0.0";
            $output->writeln("Starting development server @ http://localhost:{$port}/");
            $ips = explode(' ', exec('hostname -I'));
            foreach ($ips as $ip) {
                $output->writeln("                              http://{$ip}:{$port}/");
            }
        } else {
            $output->writeln("Starting development server @ http://{$host}:{$port}/");
        }
        $cmd = "php -S {$host}:{$port} -t public/ public/index.php";
        chdir(Environment::getInstance()->getPath());
        passthru($cmd);
    }


}