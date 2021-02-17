<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface source
 * @property string algo
 */
class Hash extends PluginFieldBase {

  protected static $pluginId = 'hash';

  function getValue($data) {
    $algo = $this->algo;
    $source = $this->source->value($data);
    if (!is_scalar($source)) {
      $this->error("Cannot calculate hash from non-scalar value");
    }
    return hash($algo, $data, FALSE);
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'source' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field'],
      'algo' => [ '_type' => 'enum', '_required' => FALSE, '_values' => hash_algos()],
    ];
  }

  static function defaultConfig($data = NULL) {
    return [
      'algo' => 'md5',
    ] + parent::defaultConfig($data);
  }
}
