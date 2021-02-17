<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface source
 * @property array do
 */
class Each extends PluginFieldBase {

  protected static $pluginId = 'each';

  /**
   * Initialize field
   */
  public function initialize() {
    // Don't bootstrap fields below
    $this->initialized = TRUE;
  }

  function getValue($data) {
    $source = $this->source->getValue($data);
    if (!is_array($source)) {
      $this->error("Value of the 'source' must be an array.");
    }
    $result = [];
    foreach($source as $key => $item) {
      if (!empty($this->do['key'])) {
        $key = $this->do['key']->getValue(['input' => $item] + $data);
      }
      $result[$key] = $this->do['value']->getValue(['input' => $item] + $data);
    }
    return $result;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'source' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
      'do' => [ '_type' => 'array', '_required' => TRUE, '_children' => [
        'key' => [ '_type' => 'partial', '_required' => FALSE, '_partial' => 'field' ],
        'value' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
      ]],
    ];
  }

}
