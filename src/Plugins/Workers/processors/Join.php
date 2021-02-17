<?php

namespace DataMincerPlugins\Workers\processors;

use DataMincerCore\Plugin\PluginFieldInterface;
use DataMincerCore\Plugin\PluginWorkerBase;

/**
 * @property PluginFieldInterface join
 * @property PluginFieldInterface else
 */
class Join extends PluginWorkerBase {

  protected static $pluginId = 'join';

  public function evaluate($data = []) {
    // Do not evaluate 'on', as it's intended for process()
    return $this->evaluateChildren($data, [], [['on'], ['else']]);
  }

  /**
   * @inheritDoc
   */
  public function process($config) {
    $data = yield;
    $keys = current($this->evaluateChildren($data, [['on']]));
    $found = FALSE;
    foreach ($config['join'] as $row) {
      $match = TRUE;
      foreach ($keys as $key => $value) {
        if (!array_key_exists($key, $row)) {
          $this->error("Key '$key' no found in the input array");
        }
        $match = $match && $row[$key] == $value;
        if (!$match) {
          break;
        }
      }
      if ($match) {
        $found = TRUE;
        yield $this->mergeResult($row, $data, $config);
      }
    }
    if (!$found && !empty($this->else)) {
      // Not a single match was found, passthrough if allowed by "inner"
      $row = current($this->evaluateChildren($data, [['else']]));
      yield $this->mergeResult($row, $data, $config);
    }
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'join' => ['_type' => 'partial', '_required' => FALSE, '_partial' => 'field'],
      'select' => ['_type' => 'prototype', '_required' => FALSE, '_prototype' => [
        '_type' => 'text', '_required' => TRUE,
      ]],
      'on' => ['_type' => 'prototype', '_required' => TRUE, '_prototype' => [
        '_type' => 'partial', '_required' => TRUE, '_partial' => 'field'
      ]],
      'else' => ['_type' => 'prototype', '_required' => FALSE, '_prototype' => [
        '_type' => 'partial', '_required' => FALSE, '_partial' => 'field'
      ]],
    ];
  }

  static function defaultConfig($data = NULL) {
    return [
      'select' => []
    ] + parent::defaultConfig($data);
  }

}
