<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface exec
 * @property PluginFieldInterface[] params
 */
class Exec extends PluginFieldBase {

  protected static $pluginId = 'exec';

  function getValue($data) {
    $cmd = $this->exec->getValue($data);
    $params = [];
    foreach ($this->params as $param) {
      $params[] = escapeshellarg($param->getValue($data));
    }
    @exec($cmd . ' ' . implode(' ', $params) . ' 2>&1 ', $output, $return);
    if ($return !== 0) {
      $this->error("Got error while executing command:\n\t$cmd\n\tOutput: " . implode($output));
    }
    return TRUE;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
        'exec' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
        'params' => [ '_type' => 'prototype', '_required' => FALSE, '_min_items' => 1, '_prototype' => [
          '_type' => 'partial', '_required' => TRUE, '_partial' => 'field',
        ]],
      ];
  }

}
