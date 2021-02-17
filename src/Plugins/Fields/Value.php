<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;

/**
 * @property string value
 */
class Value extends PluginFieldBase {

  protected static $pluginId = 'value';
  protected static $isDefault = TRUE;

  function getValue($data) {
    $value = $this->value;
    return $this->resolveParam($data, $value);
  }

  static function defaultConfig($data = NULL) {
    $result = parent::defaultConfig($data);
    if (!is_null($data)) {
      $result['value'] = $data;
    }
    return $result;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'value' => [ '_type' => 'choice', '_choices' => [
        'text' => ['_type' => 'text', '_required' => TRUE],
        'array' => ['_type' => 'array', '_required' => TRUE, '_ignore_extra_keys' => TRUE, '_children' => []],
      ]],
    ];
  }

}
