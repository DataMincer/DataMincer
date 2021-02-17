<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;

/**
 * @property array|string source
 * @property array items
 */
class Append extends PluginFieldBase {

  protected static $pluginId = 'append';

  function getValue($data) {
    $result = $this->resolveParams($data, $this->source);
    if (!is_array($result)) {
      $this->error('The source must be an array');
    }
    foreach ($this->items as $item) {
      array_push($result, $item);
    }
    return $result;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'source' => [ '_type' => 'choice', '_choices' => [
        'array' =>   [ '_type' => 'prototype', '_required' => TRUE, '_min_items' => 1, '_prototype' => [
          '_type' => 'text', '_required' => TRUE]],
        'single' =>  [ '_type' => 'text', '_required' => TRUE ]
      ]],
      'items' => [ '_type' => 'choice', '_choices' => [
        'array' =>   [ '_type' => 'prototype', '_required' => TRUE, '_min_items' => 1, '_prototype' => [
          '_type' => 'text', '_required' => TRUE]],
        'single' =>  [ '_type' => 'text', '_required' => TRUE ]
      ]],
    ];
  }

}
