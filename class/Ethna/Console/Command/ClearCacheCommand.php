<?php
namespace Ethna\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Ethna_Controller;
use Ethna_Generator;
use Ethna_Util;
use Ethna_Handle;
use Ethna;

class ClearCacheCommand extends AddActionCommand
{
    protected function configure()
    {
        $this->setName('clear-cache')
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

        $basedir = isset($args['basedir']) ? realpath(end($args['basedir'])) : getcwd();
        $controller = Ethna_Handle::getAppController($basedir);
        if (Ethna::isError($controller)) {
            return $controller;
        }
        $tmp_dir = $controller->getDirectory('tmp');

        if (isset($args['smarty']) || isset($args['any-tmp-files'])) {
            echo "cleaning smarty caches, compiled templates...";
            $renderer = $controller->getRenderer();
            if (strtolower(get_class($renderer)) == "ethna_renderer_smarty") {
                $renderer->getEngine()->clear_all_cache();
                $renderer->getEngine()->clear_compiled_tpl();
            }
            echo " done\n";
        }


        if (isset($args['cachemanager']) || isset($args['any-tmp-files'])) {
            echo "cleaning Ethna_Plugin_Cachemanager caches...";
            $cache_dir = sprintf("%s/cache", $tmp_dir);
            Ethna_Util::purgeDir($cache_dir);
            echo " done\n";
        }

        if (isset($args['any-tmp-files'])) {
            echo "cleaning tmp dirs...";
            // purge only entries in tmp.
            if ($dh = opendir($tmp_dir)) {
                while (($entry = readdir($dh)) !== false) {
                    if ($entry === '.' || $entry === '..') {
                        continue;
                    }
                    Ethna_Util::purgeDir("{$tmp_dir}/{$entry}");
                }
                closedir($dh);
            }
            echo " done\n";
        }

        return true;
    }
}