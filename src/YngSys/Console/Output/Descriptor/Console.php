<?php
namespace Yng\Console\Output\Descriptor;

use Yng\Console as YngConsole;
use Yng\Console\Command;

class Console
{

    const GLOBAL_NAMESPACE = '_global';

    /**
     * @var YngConsole
     */
    private $console;

    /**
     * @var null|string
     */
    private $namespace;

    /**
     * @var array
     */
    private $namespaces;

    /**
     * @var Command[]
     */
    private $commands;

    /**
     * @var Command[]
     */
    private $aliases;

    /**
     * 构造方法
     * @param YngConsole $console
     * @param string|null  $namespace
     */
    public function __construct(YngConsole $console, $namespace = null)
    {
        $this->console   = $console;
        $this->namespace = $namespace;
    }

    /**
     * @return array
     */
    public function getNamespaces(): array
    {
        if (null === $this->namespaces) {
            $this->inspectConsole();
        }

        return $this->namespaces;
    }

    /**
     * @return Command[]
     */
    public function getCommands(): array
    {
        if (null === $this->commands) {
            $this->inspectConsole();
        }

        return $this->commands;
    }

    /**
     * @param string $name
     * @return Command
     * @throws \InvalidArgumentException
     */
    public function getCommand(string $name): Command
    {
        if (!isset($this->commands[$name]) && !isset($this->aliases[$name])) {
            throw new \InvalidArgumentException(sprintf('Command %s does not exist.', $name));
        }

        return $this->commands[$name] ?? $this->aliases[$name];
    }

    private function inspectConsole(): void
    {
        $this->commands   = [];
        $this->namespaces = [];

        $all = $this->console->all($this->namespace ? $this->console->findNamespace($this->namespace) : null);
        foreach ($this->sortCommands($all) as $namespace => $commands) {
            $names = [];

            /** @var Command $command */
            foreach ($commands as $name => $command) {
                if (is_string($command)) {
                    $command = new $command();
                }

                if (!$command->getName()) {
                    continue;
                }

                if ($command->getName() === $name) {
                    $this->commands[$name] = $command;
                } else {
                    $this->aliases[$name] = $command;
                }

                $names[] = $name;
            }

            $this->namespaces[$namespace] = ['id' => $namespace, 'commands' => $names];
        }
    }

    /**
     * @param array $commands
     * @return array
     */
    private function sortCommands(array $commands): array
    {
        $namespacedCommands = [];
        foreach ($commands as $name => $command) {
            $key = $this->console->extractNamespace($name, 1);
            if (!$key) {
                $key = self::GLOBAL_NAMESPACE;
            }

            $namespacedCommands[$key][$name] = $command;
        }
        ksort($namespacedCommands);

        foreach ($namespacedCommands as &$commandsSet) {
            ksort($commandsSet);
        }
        // unset reference to keep scope clear
        unset($commandsSet);

        return $namespacedCommands;
    }
}
