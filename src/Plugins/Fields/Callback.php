<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;

/**
 * @property callable $callable
 */
class Callback extends PluginFieldBase {

  protected static $pluginId = 'callback';

  function getValue($data) {
    if (!is_callable($this->callable)) {
      $text = is_array($this->callable) ? implode('::', $this->callable) : $this->callable;
      $this->error("The callback '$text' is not valid.");
    }
    $params = $this->_config['params'];
    $params = $this->resolveParams($data, $params);
    return call_user_func_array($this->callable, $params);
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'callable' => [ '_type' => 'choice', '_required' => TRUE, '_choices' => [
        'function' => [ '_type' => 'text', '_required' => TRUE ],
        'method' => [ '_type' => 'prototype', '_required' => TRUE, '_min_items' => 2, '_max_items' => 2, '_prototype' => [
          '_type' => 'text', '_required' => TRUE,
        ]],
      ]],
      'params' => [ '_type' => 'prototype', '_required' => TRUE, '_min_items' => 1, '_prototype' => [
        '_type' => 'text', '_required' => TRUE,
      ]],

    ];
  }

}
