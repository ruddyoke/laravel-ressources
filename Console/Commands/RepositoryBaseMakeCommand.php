<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class RepositoryBaseMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:repository-base';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a base ressource repository.'; 

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Base Repository';

    public function fire()
    {
        $name = $this->parseName('BaseRepository');

        $path = $this->getPath($name);

        if ($this->alreadyExists('BaseRepository')) {
            $this->error($this->type.' already exists!');

            return true;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->info($this->type.' created successfully.');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/repository-base.stub';
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $stub;
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

}
