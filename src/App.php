<?php

namespace Kobens\Core;

use CliArgs\CliArgs;
use Kobens\Core\ActionInterface;
use Kobens\Core\App\Resources;
use Kobens\Core\App\ResourcesInterface;
use Kobens\Core\Exception\RuntimeArgsInvalidException;
use Kobens\Core\Output;
use Zend\Config\Config;
use Zend\Config\Reader\Xml;

abstract class App
{
    /**
     * @var CliArgs
     */
    protected $cli;

    /**
     * @var ResourcesInterface
     */
    protected $appResources;

    /**
     * @var Output
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
        'showActions' => [
            'help' => 'Show all available actions',
        ],
        'config' => [
            'alias' => 'c',
            'help' => 'What config file to load environment settings from',
        ],
    ];

    public function __construct()
    {
        $this->cli = new CliArgs($this->cliArgs);
        $this->output = new Output();
    }

    private function init() : void
    {
        $this->appResources = new Resources(
            $this->output,
            $this->getConfig(),
        );
    }

    abstract protected function getAvailableActions() : array;

    final public function run() : void
    {
        if ($this->cli->isFlagExist('h')) {
            $this->output->write($this->cli->getHelp());
        } elseif ($this->cli->isFlagExist('showActions')) {
            $this->showActionHelp();
        } else {
            $this->init();
            try {
                $action = $this->getAction();
                if ($action->getRuntimeArgOptions()) {
                    $args = new CliArgs($action->getRuntimeArgOptions());
                    $action->setRuntimeArgs($args->getArgs());
                }
                $action->execute();
            } catch (RuntimeArgsInvalidException $e) {
                $this->output->write($e->getMessage());
            } catch (\Exception $e) {
                $this->output->writeException($e);
            }
        }
    }

    private function showActionHelp() : App
    {
        $this->output->write('Available Actions:');
        foreach ($this->getAvailableActions() as $actionName => $actionInfo) {
            $this->output->write(\sprintf(
                "\t\"%s\"\t\"%s\"",
                $actionName,
                $actionInfo['description']
            ));
        }
        return $this;
    }


    /**
     * @throws Exception\ActionRequiredException
     * @throws Exception\ActionInvalidException
     */
    private function getAction() : ActionInterface
    {
        $action = (string) $this->cli->getArg('action');
        $action = \trim($action);
        if ($action === '') {
            throw new Exception\ActionRequiredException();
        }
        $actions = $this->getAvailableActions();
        if (!\array_key_exists($action, $actions)) {
            throw new Exception\ActionInvalidException($action);
        }
        return new $actions[$action]['class']($this->appResources);
    }

    private function getConfig() : Config
    {
        $array = (new Xml())->fromFile((string) $this->cli->getArg('config'));
        return new Config($array);
    }

}