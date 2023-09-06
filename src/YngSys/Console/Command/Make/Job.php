<?php
namespace Yng\Console\Command\Make;

use Yng\Console\Command\Make;

class Job extends Make
{

    protected $type = "Job";

    protected function configure()
    {
        parent::configure();
        $this->setName('make:job')->setDescription('Create a new Job class');
    }

    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR .'job.stub';
    }


    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\Job';
    }

}
