<?php

namespace DataMincerPlugins\Generators;

use DataMincerCore\Plugin\PluginGeneratorBase;

class DefaultGenerator extends PluginGeneratorBase {

  protected static $pluginId = 'default';
  protected static $isDefault = TRUE;

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
        'vars' => ['_type' => 'prototype', '_required' => FALSE, '_min_items' => 1, '_prototype' => [
          '_type' => 'partial', '_required' => TRUE, '_partial' => 'field',
        ]],
      ];
  }

}
