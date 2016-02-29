<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class RepositoryMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a model repository.'; 

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';


    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        if (!$this->alreadyExists('BaseRepository')) {
            $this->call('make:repository-base', ['name'=>'BaseRepository']);
        }

        parent::fire();
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/repository.stub';
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)
        ->replaceModel($stub)
        ->replaceClass($stub, $name);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Repositories';
    }

    protected function _getTableName()
    {
        return str_replace('Repository', '', $this->getNameInput());
    }

    protected function replaceModel(&$stub)
    {
        $stub = str_replace('DummyModel', $this->_getTableName(), $stub);
        // $this->output->writeln("<info>".$type.":</info>OK");
        return $this;
    }

}
