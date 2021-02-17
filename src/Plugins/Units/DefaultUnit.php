<?php

namespace DataMincerPlugins\Units;

use DataMincerCore\Plugin\PluginUnitBase;

class DefaultUnit extends PluginUnitBase {

  protected static $pluginId = 'default';
  protected static $isDefault = TRUE;

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'version' => ['_type' => 'text', '_required' => FALSE],
      'global' => ['_type' => 'prototype', '_required' => FALSE, '_min_items' => 1, '_prototype' => [
        '_type' => 'partial', '_required' => TRUE, '_partial' => 'field',
      ]],
    ];
  }

}
