<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface source
 * @property string by
 */
class Group extends PluginFieldBase {

  protected static $pluginId = 'group';

  function getValue($data) {
    $source = $this->source->value($data);
    $by = $this->resolveParam($data, $this->by);
    $result = [];
    foreach ($source as $index => $info) {
      $key = array_splice($info, $by, 1);
      if (count($info) == 1) {
        $info = current($info);
      }
      $result[current($key)] = $info;
    }
    return $result;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'source' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
      'by' => [ '_type' => 'text', '_required' => TRUE ],
    ];
  }

}
