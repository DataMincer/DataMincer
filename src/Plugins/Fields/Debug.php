<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\DataMincer;
use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Util;
use DataMincerPlugins\Traits\DebugTrait;

/**
 * @property string value
 * @property bool dense
 */
class Debug extends PluginFieldBase {

  use DebugTrait;

  protected static $pluginId = 'debug';

  function getValue($data) {
    if (!empty($this->select)) {
      $debug_data = $this->select->getValue($data);
    }
    else {
      $debug_data = $data;
    }
    $debug_data = $this->prepareData($debug_data, $this->dense);
    DataMincer::logger()->info(Util::toJson($debug_data));
    return $debug_data;
  }

}
