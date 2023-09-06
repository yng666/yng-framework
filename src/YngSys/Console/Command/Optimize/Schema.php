<?php

namespace Yng\Console\Command\Optimize;

use Exception;
use Yng\Console\Command;
use Yng\Console\Input;
use Yng\Console\Input\Argument;
use Yng\Console\Input\Option;
use Yng\Console\Output;
use Yng\Db\PDOConnection;

class Schema extends Command
{
    protected function configure()
    {
        $this->setName('optimize:schema')
            ->addArgument('dir', Argument::OPTIONAL, 'dir name .')
            ->addOption('connection', null, Option::VALUE_REQUIRED, 'connection name .')
            ->addOption('table', null, Option::VALUE_REQUIRED, 'table name .')
            ->setDescription('Build database schema cache.');
    }

    protected function execute(Input $input, Output $output)
    {
        $dir = $input->getArgument('dir') ?: '';

        if ($input->hasOption('table')) {
            $connection = $this->app->db->connect($input->getOption('connection'));
            if (!$connection instanceof PDOConnection) {
                $output->error("only PDO connection support schema cache!");
                return;
            }
            $table = $input->getOption('table');
            if (!str_contains($table, '.')) {
                $dbName = $connection->getConfig('database');
            } else {
                [$dbName, $table] = explode('.', $table);
            }

            if ($table == '*') {
                $table = $connection->getTables($dbName);
            }

            $this->buildDataBaseSchema($connection, (array) $table, $dbName);
        } else {
            if ($dir) {
                $appPath   = $this->app->getBasePath() . $dir . DIRECTORY_SEPARATOR;
                $namespace = 'App\\' . $dir;
            } else {
                $appPath   = $this->app->getBasePath();
                $namespace = 'App';
            }

            $path = $appPath . 'model';
            $list = is_dir($path) ? scandir($path) : [];

            foreach ($list as $file) {
                if (str_starts_with($file, '.')) {
                    continue;
                }
                $class = '\\' . $namespace . '\\Model\\' . pathinfo($file, PATHINFO_FILENAME);
                
                if (!class_exists($class)) {
                    continue;
                }

                $this->buildModelSchema($class);
            }
        }

        $output->writeln('<info>Succeed!</info>');
    }

    protected function buildModelSchema(string $class): void
    {
        $reflect = new \ReflectionClass($class);
        if (!$reflect->isAbstract() && $reflect->isSubclassOf('\Yng\Model')) {
            try {
                /** @var \Yng\Model $model */
                $model      = new $class;
                $connection = $model->db()->getConnection();
                if ($connection instanceof PDOConnection) {
                    $table = $model->getTable();
                    //预读字段信息
                    $connection->getSchemaInfo($table, true);
                }
            } catch (Exception $e) {

            }
        }
    }

    protected function buildDataBaseSchema(PDOConnection $connection, array $tables, string $dbName): void
    {
        foreach ($tables as $table) {
            //预读字段信息
            $connection->getSchemaInfo("{$dbName}.{$table}", true);
        }
    }
}
