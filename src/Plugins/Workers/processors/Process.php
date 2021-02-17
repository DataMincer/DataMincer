<?php

namespace DataMincerPlugins\Workers\processors;

use DataMincerCore\Plugin\PluginWorkerBase;

class Process extends PluginWorkerBase {

  protected static $pluginId = 'process';

  public function evaluate($data = []) {
    // Do not evaluate 'fields' field, as it's intended for process()
    return $this->evaluateChildren($data, [], [['fields']]);
  }

  /**
   * @inheritDoc
   */
  public function process($config) {
    $data = yield;
    $values = $this->evaluateChildren($data);
    yield $this->mergeResult($values['fields'], $data, $values);
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'vars' => ['_type' => 'prototype', '_required' => FALSE, '_prototype' => [
        '_type' => 'partial', '_required' => TRUE, '_partial' => 'field',
      ]],
      'fields' => ['_type' => 'prototype', '_required' => TRUE, '_prototype' => [
        '_type' => 'partial', '_required' => TRUE, '_partial' => 'field',
      ]],
    ];
  }

}
