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

class AddActionCommand extends Command
{
    protected function configure()
    {
        $this->setName('add-action')
            ->addArgument("action", null, InputOption::VALUE_REQUIRED, "action name")
            ->addOption("basedir", null, InputOption::VALUE_OPTIONAL, "base dir")
            ->addOption("skelfile", null, InputOption::VALUE_OPTIONAL, "action class skelton")
            ->addOption("gateway", "www", InputOption::VALUE_OPTIONAL, "www|cli|xmlrpc")
            ->addOption("with-unittest", false, InputOption::VALUE_OPTIONAL, "create unit test")
            ->addOption("unittestskle", null, InputOption::VALUE_OPTIONAL, "unit test skelton")
            ->setDescription('add new action to project');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action_name = $input->getArgument("action");
        $opt_list = array();

        $r = Ethna_Controller::checkActionName($action_name);
        if (Ethna::isError($r)) {
            return $r;
        }

        $result = $this->perform('Action', $action_name, $opt_list);
        if (Ethna::isError($result)) {
            $output->writeln($result->getMessage());
        }

    }

    protected function perform($target, $target_name, $opt_list)
    {
        // basedir
        if (isset($opt_list['basedir'])) {
            $basedir = realpath(end($opt_list['basedir']));
        } else {
            $basedir = getcwd();
        }

        // skelfile
        if (isset($opt_list['skelfile'])) {
            $skelfile = end($opt_list['skelfile']);
        } else {
            $skelfile = null;
        }

        // gateway
        if (isset($opt_list['gateway'])) {
            $gateway = 'GATEWAY_' . strtoupper(end($opt_list['gateway']));
            if (defined($gateway)) {
                $gateway = constant($gateway);
            } else {
                return Ethna::raiseError('unknown gateway', 'usage');
            }
        } else {
            $gateway = GATEWAY_WWW;
        }

        //  possible target is Action, View.
        $r = Ethna_Generator::generate($target, $basedir,
            $target_name, $skelfile, $gateway);
        if (Ethna::isError($r)) {
            printf("error occurred while generating skelton. please see also following error message(s)\n\n");
            return $r;
        }

        //
        //  if specified, generate corresponding testcase,
        //  except for template.
        //
        if ($target != 'Template' && isset($opt_list['with-unittest'])) {
            $testskel = (isset($opt_list['unittestskel']))
                ? end($opt_list['unittestskel'])
                : null;
            $r = Ethna_Generator::generate("{$target}Test", $basedir, $target_name, $testskel, $gateway);
            if (Ethna::isError($r)) {
                printf("error occurred while generating action test skelton. please see also following error message(s)\n\n");
                return $r;
            }
        }

        $true = true;
        return $true;
    }
}