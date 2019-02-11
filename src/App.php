<?php

namespace Kobens\Core;

abstract class App
{
    /**
     * @var \CliArgs\CliArgs
     */
    protected $cli;

    /**
     * @var \Kobens\Core\App\ResourcesInterface
     */
    protected $appResources;

    /**
     * @var \Kobens\Core\Output
     */
    protected $output;

    /**
     * @var array
     */
    protected $cliArgs = [
        'help' => [
            'alias' => 'h',
            'help' => 'Show help about all options',
        ],
        'action' => [
            'alias' => 'a',
            'help' => 'What action to perform. Use "showActions" to list valid actions',
        ],
        'config' => [
            'alias' => 'c',
            'help' => 'What config file to load environment settings from',
        ],
    ];

    public function __construct()
    {
        $this->cli = new \CliArgs\CliArgs($this->cliArgs);
        $this->output = new \Kobens\Core\Output();
    }

    private function init()
    {
        $config = $this->getConfig();
        $this->appResources = new \Kobens\Core\App\Resources(
            new \Kobens\Core\Db\Adapter($config->get('database')->toArray()),
            $this->output,
            $config
        );
    }

    /**
     * @return array
     */
    abstract protected function getAvailableActions() : array;

    final public function run()
    {
        if ($this->cli->isFlagExist('h')) {
            $this->output->write($this->cli->getHelp());
        } else {
            $this->init();
            try {
                $action = $this->getAction();
                if ($action->getRuntimeArgOptions()) {
                    $args = new \CliArgs\CliArgs($action->getRuntimeArgOptions());
                    $action->setRuntimeArgs($args->getArgs());
                }
                $action->execute();
            } catch (\Kobens\Core\Exception\RuntimeArgsInvalidException $e) {
                $this->output->write($e->getMessage());
            } catch (\Exception $e) {
                $this->output->writeException($e);
            }
        }
    }

    /**
     * @throws Exception\ActionRequiredException
     * @throws Exception\ActionInvalidException
     * @return \Kobens\Core\ActionInterface
     */
    private function getAction() : \Kobens\Core\ActionInterface
    {
        $action = (string) $this->cli->getArg('action');
        $action = trim($action);
        if ($action === '') {
            throw new Exception\ActionRequiredException();
        }
        $actions = $this->getAvailableActions();
        if (!\array_key_exists($action, $actions)) {
            throw new Exception\ActionInvalidException($action);
        }
        return new $actions[$action]($this->appResources);
    }

    /**
     * @return \Zend\Config\Config
     */
    private function getConfig() : \Zend\Config\Config
    {
        $filename = (string) $this->cli->getArg('config');
        $reader = new \Zend\Config\Reader\Xml();
        $array = $reader->fromFile($filename);
        return new \Zend\Config\Config($array);
    }

}