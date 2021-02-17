<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;
use DataMincerPlugins\Traits\CsvTrait;

/**
 * @property PluginFieldInterface path
 * @property string format
 */
class Csv extends PluginFieldBase {

  use CsvTrait;

  protected static $pluginId = 'csv';

  function getValue($data) {
    $result = [];
    $config = $this->evaluateChildren($data);
    foreach ($this->getRecords($config, $data) as $record) {
      $result[] = $record;
    }
    return $result;
  }


}
