<?php

namespace DataMincerPlugins\Workers\readers;

use DataMincerCore\Plugin\PluginWorkerBase;

/**
 * @property int count
 */
class Sequence extends PluginWorkerBase {

  protected static $pluginId = 'sequence';

  /**
   * @inheritDoc
   */
  public function generate1($values, $row_data) {
    for ($i = 0; $i < $values['count']; $i++) {
      yield ['counter' => $i];
    }
  }

  /**
   * @inheritDoc
   */
  static function getSchemaChildren() {
    return [
      'count' => [ '_type' => 'number', '_required' => TRUE ]
    ];
  }

  /**
   * @inheritDoc
   */
  static function defaultConfig($data = NULL) {
    return [ ];
  }

}
