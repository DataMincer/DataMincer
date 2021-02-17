<?php

namespace DataMincerPlugins\Workers\processors;

use DataMincerCore\DataMincer;
use DataMincerCore\Plugin\PluginFieldInterface;
use DataMincerCore\Plugin\PluginWorkerBase;
use DataMincerCore\Util;
use DataMincerPlugins\Traits\DebugTrait;

/**
 * @property bool dense
 * @property PluginFieldInterface select
 */
class Debug extends PluginWorkerBase {

  use DebugTrait;

  protected static $pluginId = 'debug';

  public function evaluate($data = []) {
    // Do not evaluate 'select' field, as it's intended for process()
    return $this->evaluateChildren($data, [], [['select']]);
  }

  /**
   * @inheritDoc
   */
  public function process($config) {
    $data = yield;
    if (!empty($this->select)) {
      $debug_data = $this->select->getValue($data);
    }
    else {
      $debug_data = $data;
    }
    $debug_data = $this->prepareData($debug_data, $config['dense']);
    DataMincer::logger()->info(Util::toJson($debug_data));
    yield $data;
  }

}
