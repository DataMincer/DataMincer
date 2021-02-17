<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Util;

/**
 * @property array items
 * @property bool sort
 */
class Merge extends PluginFieldBase {

  protected static $pluginId = 'merge';

  function getValue($data) {
    $items = $this->resolveParams($data, $this->items);
    $result = Util::arrayMergeDeepArray($items);
    if ($this->sort) {
      sort($result,SORT_STRING);
    }
    return $result;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'items' => [ '_type' => 'prototype', '_required' => TRUE, '_min_items' => 1, '_prototype' => [
        '_type' => 'text', '_required' => TRUE,
      ]],
      'sort' => [ '_type' => 'boolean', '_required' => FALSE ]
    ];
  }

}
