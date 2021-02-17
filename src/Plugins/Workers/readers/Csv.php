<?php

namespace DataMincerPlugins\Workers\readers;

use DataMincerPlugins\Traits\CsvTrait;
use DataMincerCore\Plugin\PluginFieldInterface;
use DataMincerCore\Plugin\PluginWorkerBase;

/**
 * @property array columns
 * @property PluginFieldInterface path
 * @property int header_offset
 */
class Csv extends PluginWorkerBase {

  use CsvTrait;

  protected static $pluginId = 'csv';

  /**
   * @inheritDoc
   */
  public function process($config) {
    $data = yield;
    foreach($this->getRecords($config, $data) as $row) {
      yield $this->mergeResult($row, $data, $config);
    }
  }

}
