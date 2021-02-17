<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Exception\PluginException;
use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface source
 * @property string[] levels
 */
class Balance extends PluginFieldBase {

  protected static $pluginId = 'balance';

  function getValue($data) {
    $source = $this->source->getValue($data);
    $levels = $this->resolveParam($data, $this->levels);
    return $this->balanceTree($source, $levels);
  }

  /**
   * @param $tree
   * @param $levels
   * @return array
   * @throws PluginException
   */
  function balanceTree($tree, $levels) {
    $level = array_shift($levels);
    $result = [];
    foreach ($tree as $key => $value) {
      if (!empty($levels)) {
        if (!is_array($value)) {
          $this->error('Cannot balance non-array: ' . $value);
        }
        foreach($this->balanceTree($value, $levels) as $row) {
          $result[] = array_merge([$level => $key], $row);
        }
      }
      else {
        if (!empty($level)) {
          $result[] = array_merge([$level => $key], $value);
        }
        else {
          $result[] = $value;
        }
      }
    }
    return $result;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'source' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
      'levels' => [ '_type' => 'prototype', '_required' => TRUE, '_prototype' => [
        '_type' => 'text', '_required' => TRUE,
      ]]
    ];
  }

}
