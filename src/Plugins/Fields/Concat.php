<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;

/**
 * @property array items
 * @property string glue
 * @property bool filter
 */
class Concat extends PluginFieldBase {

  protected static $pluginId = 'concat';

  function getValue($data) {
    $items = $this->resolveParams($data, $this->items);
    if ($this->filter) {
      $items = array_filter($items);
    }
    return implode($this->glue ?? '', $items);
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'items' => [ '_type' => 'choice', '_choices' => [
        'array' =>   [ '_type' => 'prototype', '_required' => TRUE, '_min_items' => 1, '_prototype' => [
          '_type' => 'text', '_required' => TRUE]],
        'single' =>  [ '_type' => 'text', '_required' => TRUE ]
      ]],
      'glue' => [ '_type' => 'text', '_required' => FALSE ],
      'filter' => [ '_type' => 'boolean', '_required' => FALSE ]
    ];
  }

  static function defaultConfig($data = NULL) {
    return [
        'glue' => '',
        'filter' => FALSE,
      ] + parent::defaultConfig($data);
  }

}
