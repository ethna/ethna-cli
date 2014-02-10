<?php
/*
 * Copyright (C) the Ethna contributors. All rights reserved.
 *
 * This file is part of the Ethna package, distributed under new BSD.
 * For full terms see the included LICENSE file.
 */

namespace Ethna\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Application extends BaseApplication
{
    public function __construct($version)
    {
        parent::__construct("ethna", $version);
        $this->setupCommands();
    }

    public function setupCommands()
    {
        $this->add(new Command\AddActionCommand());
        $this->add(new Command\AddTemplateCommand());
        $this->add(new Command\AddEntryPointCommand());
        $this->add(new Command\AddViewCommand());
        $this->add(new Command\ClearCacheCommand());
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        return parent::doRun($input, $output);
    }
}