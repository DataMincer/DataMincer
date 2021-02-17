<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface list
 * @property array by
 */
class GroupBy extends PluginFieldBase {

  protected static $pluginId = 'groupby';

  function getValue($data) {
    $list = $this->list->value($data);
    $by = $this->resolveParams($data, $this->by);
    return  $this->recursiveGroupBy($list, $by);
  }

  protected function recursiveGroupBy($array, $keys) {
    $key = array_shift($keys);
    $result = [];
    foreach ($array as $val) {
      if (array_key_exists($key, $val)) {
        $result[$val[$key]][] = $val;
      } else {
        $result[""][] = $val;
      }
    }
    if (count($keys)) {
      foreach ($result as &$subarray) {
        $subarray = $this->recursiveGroupBy($subarray, $keys);
      }
    }

    return $result;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'list' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
      'by' => [ '_type' => 'prototype', '_required' => TRUE, '_min_items' => 1, '_prototype' => [
        '_type' => 'text', '_required' => TRUE
      ]],

    ];
  }

}
