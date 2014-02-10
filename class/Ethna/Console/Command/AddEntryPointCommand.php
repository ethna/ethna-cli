<?php
namespace Ethna\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Ethna_Controller;
use Ethna_Generator;
use Ethna;

class AddEntryPointCommand extends AddActionCommand
{
    protected function configure()
    {
        $this->setName('add-entrypoint')
            ->addArgument("action", null, InputOption::VALUE_REQUIRED, "action name")
            ->addOption("basedir", null, InputOption::VALUE_OPTIONAL, "base dir")
            ->addOption("skelfile", null, InputOption::VALUE_OPTIONAL, "action class skelton")
            ->addOption("locale", "ja_JP", InputOption::VALUE_OPTIONAL, "locale")
            ->addOption("encoding", "UTF-8", InputOption::VALUE_OPTIONAL, "utf8")
            ->addOption("gateway", "www", InputOption::VALUE_OPTIONAL, "www|cli|xmlrpc")
            ->setDescription('add new template to project');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action_name = $input->getArgument("action");
        $opt_list = array();

        $r = Ethna_Controller::checkActionName($action_name);
        if (Ethna::isError($r)) {
            return $r;
        }

        $result = $this->perform('EntryPoint', $action_name, $opt_list);
        if (Ethna::isError($result)) {
            $output->writeln($result->getMessage());
        }

        $result = $this->perform('Action', $action_name, $opt_list);
        if (Ethna::isError($result)) {
            $output->writeln($result->getMessage());
        }
    }
}