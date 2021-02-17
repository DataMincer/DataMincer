<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;

/**
 * @property string pattern
 * @property string text
 */
class Split extends PluginFieldBase {

  protected static $pluginId = 'split';

  function getValue($data) {
    return preg_split('~' . $this->pattern . '~', $this->resolveParam($data, $this->text));
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'pattern' => [ '_type' => 'text', '_required' => TRUE ],
      'text' => [ '_type' => 'text', '_required' => TRUE ]
    ];
  }

}
