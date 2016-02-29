<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand as BaseModelCommand;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Facades\Schema as Schema;

class ModelMakeCommand extends BaseModelCommand
{

    protected $exclude = ['id', 'password', 'created_at', 'updated_at', 'deleted_at'];
    protected $_date   = ['created_at', 'updated_at', 'deleted_at'];
    protected $_timestamps_fields   = ['created_at', 'updated_at'];

    protected function getStub()
    {
        return __DIR__.'/stubs/model.stub';
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)
        ->replaceFillable($stub)
        ->replaceDate($stub)
        ->replaceIncrementing($stub)
        ->replaceTimestamps($stub)
        ->replaceClass($stub, $name);
    }

    protected function _getTableName()
    {
        
        if(!empty($this->option('table'))) 
            $table = str_plural(strtolower($this->option('table')));
        else
        {
            // On construit le nom de la table à partir du nom du modèle
            $name = last( explode('/', $this->getNameInput()) );
            $table = str_plural(strtolower($name));
        }
        return $table;
    }

    protected function replaceFillable(&$stub)
    {
        if($this->input->getOption('fillable') || $this->option('all'))
        {
            // On récupère le nom des colonnes
            $columns = Schema::getColumnListing($this->_getTableName());
            // On exclut les colonnes non désirées 
            $columns = array_filter($columns, function($value)
            {
                return !in_array($value, $this->exclude);
            });
            // On ajoute des apostrophes
            array_walk($columns, function(&$value) {
                $value = "'" . $value . "'";
            });
            // CSV format
            $columns = implode(',', $columns);
        }

        $stub = str_replace('DummyFillable', isset($columns)? $columns : '', $stub);
        $this->output->writeln("<info>Fillable:</info>OK");
        return $this;
    }

    protected function replaceDate(&$stub)
    {
        if($this->option('date') || $this->option('all'))
        {
            // On récupère le nom des colonnes
            $columns = Schema::getColumnListing($this->_getTableName());
            // On recupère que les colonnes dates présentes dans la table
            $columns = array_filter($columns, function($value)
            {
                return in_array($value, $this->_date);
            });
            // On ajoute des apostrophes
            array_walk($columns, function(&$value) {
                $value = "'" . $value . "'";
            });
            // CSV format
            $columns = implode(',', $columns);
        }

        $stub = str_replace('DummyDates', isset($columns)? $columns : '', $stub);
        $this->output->writeln("<info>Dates:</info> OK");
        return $this;
    }

    protected function replaceIncrementing(&$stub)
    {
        $incrementing = true;

        if($this->option('other') || $this->option('all') )
        {
            $schema = \DB::getDoctrineSchemaManager();
            $tables = $schema->listTables();

            foreach ($tables as $table) 
            {
                if( $table->getName() == $this->_getTableName())
                {
                    foreach ($table->getColumns() as $column) 
                    {
                        if ( $column->getName() == 'id')
                        {
                            if ( $column->getType()->getName() != 'integer' ) $incrementing = false;
                            break;
                        }
                    }
                    break;
                }
            }
        }

        $stub = str_replace('DummyIncrementing', $incrementing? 'true' : 'false', $stub);
        $this->output->writeln("<info>Incrementing:</info> OK");
        return $this;
    }

    protected function replaceTimestamps(&$stub)
    {
        $columns = [];

        if($this->option('other') || $this->option('all') )
        {
            // On récupère le nom des colonnes
            $columns = Schema::getColumnListing($this->_getTableName());
            // On recupère que les colonnes dates présentes dans la table
            $columns = array_filter($columns, function($value)
            {
                return in_array($value, $this->_timestamps_fields);
            });
        }

        $stub = str_replace('DummyTimestamps', !empty($columns)? 'true' : 'false', $stub);
        $this->output->writeln("<info>Timestamps:</info> OK");
        return $this;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Models';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['migration', 'm',  InputOption::VALUE_NONE, 'Create a new migration file for the model.'],
            ['fillable', 'f',   InputOption::VALUE_NONE, 'Set the fillable columns.', null],
            ['date', 'd',       InputOption::VALUE_NONE, 'Set the date columns.', null],
            ['other', 'o',      InputOption::VALUE_NONE, 'Set the Timestamps and Incrementing.', null],
            ['all', 'a',        InputOption::VALUE_NONE, 'Make all stuff.', null],
            ['table', null,     InputOption::VALUE_OPTIONAL, 'Make all stuff.', null],
        ];
    }
}