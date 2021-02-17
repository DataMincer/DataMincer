<?php

namespace DataMincerPlugins\Workers\processors;

use DataMincerCore\Plugin\PluginFieldInterface;
use DataMincerCore\Plugin\PluginWorkerBase;

/**
 * @property PluginFieldInterface use
 */
class Iterator extends PluginWorkerBase {

  protected static $pluginId = 'iterator';

  public function evaluate($data = []) {
    return $this->evaluateChildren($data, [], [['use']]);
  }

  /**
   * @inheritDoc
   */
  public function process($config) {
    $data = yield;
    $use = $this->use->value($data);
    if (!is_iterable($use)) {
      $this->error("Cannot iterate " . gettype($use) . " variable.");
    }
    foreach($use as $row) {
      yield $this->mergeResult($row, $data, $config);
    }
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'use' => ['_type' => 'partial', '_required' => TRUE, '_partial' => 'field'],
    ];
  }

}
