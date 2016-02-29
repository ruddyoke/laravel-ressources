<?php

namespace App\Console\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand as BaseControllerCommand;
use Symfony\Component\Console\Input\InputOption;

class ControllerMakeCommand extends BaseControllerCommand
{
    protected function getStub()
    {
        if ($this->option('plain')) {
            return parent::getStub();
        }

        return __DIR__.'/stubs/controller.stub';
    }

}