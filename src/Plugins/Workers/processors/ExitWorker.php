<?php

namespace DataMincerPlugins\Workers\processors;

use DataMincerCore\Exception\PluginNoException;
use DataMincerCore\Plugin\PluginWorkerBase;

class ExitWorker extends PluginWorkerBase {

  protected static $pluginId = 'exit';

  /**
   * @inheritDoc
   * @throws PluginNoException
   */
  public function process($config) {
    yield;
    throw new PluginNoException();
  }

}
