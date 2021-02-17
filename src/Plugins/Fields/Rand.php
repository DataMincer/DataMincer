<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Exception\PluginException;
use DataMincerCore\Plugin\PluginFieldBase;

/**
 * @property array interval
 * @property bool unique
 */
class Rand extends PluginFieldBase {

  protected static $pluginId = 'rand';

  /**
   * @var array
   */
  protected $series;

  /**
   * @throws PluginException
   */
  public function setUp() {
    $interval = $this->interval;
    if ($interval[0] > $interval[1]) {
      $this->error("Wrong interval: [{$interval[0]}, {$interval[1]}]");
    }
    $this->series = $this->series($interval);
  }

  protected function series($interval) {
    $result = [];
    for ($i = $interval[0]; $i <= $interval[1]; $i++) {
      $result[] = $i;
    }
    return $result;
  }

  function getValue($data) {
    $count = count($this->series);
    if ($count == 0) {
      $this->error("No more series to generate, interval: [{$this->interval[0]}, {$this->interval[1]}]");
    }
    $index = rand(0, $count - 1);
    $result = current(array_splice($this->series, $index, 1));
    return $result;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'interval' => [ '_type' => 'array', '_required' => TRUE, '_children' => [
        0 => [ '_type' => 'text', '_required' => TRUE ],
        1 => [ '_type' => 'text', '_required' => TRUE ],
      ]]
    ];
  }

}
