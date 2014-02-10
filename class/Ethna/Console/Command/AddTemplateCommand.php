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

class AddTemplateCommand extends Command
{
    protected function configure()
    {
        $this->setName('add-template')
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

        $result = $this->perform('Template', $action_name, $opt_list);
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

        // locale
        $ctl = Ethna_Controller::getInstance();
        if (isset($opt_list['locale'])) {
            $locale = end($opt_list['locale']);
            if (!preg_match('/^[A-Za-z_]+$/', $locale)) {
                return Ethna::raiseError("You specified locale, but invalid : $locale", 'usage');
            }
        } else {
            if ($ctl instanceof Ethna_Controller) {
                $locale = $ctl->getLocale();
            } else {
                $locale = 'ja_JP';
            }
        }

        // encoding
        if (isset($opt_list['encoding'])) {
            $encoding = end($opt_list['encoding']);
            if (function_exists('mb_list_encodings')) {
                $supported_enc = mb_list_encodings();
                if (!in_array($encoding, $supported_enc)) {
                    return Ethna::raiseError("Unknown Encoding : $encoding", 'usage');
                }
            }
        } else {
            if ($ctl instanceof Ethna_Controller) {
                $encoding = $ctl->getClientEncoding();
            } else {
                $encoding = 'UTF-8';
            }
        }

        $r = Ethna_Generator::generate($target, $basedir,
            $target_name, $skelfile, $locale, $encoding);
        if (Ethna::isError($r)) {
            printf("error occurred while generating skelton. please see also following error message(s)\n\n");
            return $r;
        }

        $true = true;
        return $true;
    }
}