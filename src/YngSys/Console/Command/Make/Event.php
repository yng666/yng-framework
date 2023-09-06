<?php
namespace Yng\Console\Command\Make;

use Yng\Console\Command\Make;

class Event extends Make
{
    protected $type = "Event";

    protected function configure()
    {
        parent::configure();
        $this->setName('make:event')->setDescription('Create a new event class');
    }

    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'Stubs' . DIRECTORY_SEPARATOR . 'event.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\Event';
    }
}
