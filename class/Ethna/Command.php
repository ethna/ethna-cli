<?php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Ethna_Command extends Command
{
    protected $controller;
    protected $action_name;

    protected function configure()
    {
        $this->setName($this->controller);
        $this->ignoreValidationErrors();

        $controller = \Ethna_Handle::getAppController(getcwd());
        $action_name = $this->action_name;
        $controller->setActionCli($action_name);
        $controller->setActionName($controller->executePreActionFilter($action_name));
        $controller->setupActionForm($action_name);

        foreach ($controller->getBackend()->getActionForm()->getDef() as $key => $definition) {
            $this->addOption($key, null, InputOption::VALUE_REQUIRED);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($input->getOptions() as $key => $value) {
            $_REQUEST[$key] = $value;
        }

        $controller_name = $this->controller;
        $action_name = $this->action_name;
        $callback = function($controller_name, $action_name){
            \Ethna_Controller::main_CLI($controller_name . '_Controller', $action_name);
        };
        $this->perform($controller_name, $action_name, $callback);
    }

    protected function perform($controller_name, $action_name, $callback)
    {
        $callback($controller_name, $action_name);
    }
}