<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface source
 * @property PluginFieldInterface keys
 */
class SelectKeys extends PluginFieldBase {

  protected static $pluginId = 'selectkeys';

  function getValue($data) {
    $source = $this->source->getValue($data);
    $keys = $this->keys->getValue($data);
    return array_intersect_key($source, array_flip($keys));
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'source' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
      'keys' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
    ];
  }

}
