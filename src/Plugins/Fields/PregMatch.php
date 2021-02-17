<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface regexp
 * @property PluginFieldInterface subject
 * @property int return
 * @property string variant
 */
class PregMatch extends PluginFieldBase {

  protected static $pluginId = 'preg_match';

  function getValue($data) {
    $regexp = $this->regexp->getValue($data);
    $subject = $this->subject->getValue($data);
    $return = $this->return;
    $variant_all = !empty($this->variant) && $this->variant == 'all';
    $result = $variant_all ? [] : NULL;
    if ($variant_all) {
      $res = @preg_match_all($regexp, $subject, $matches);
    }
    else {
      $res = @preg_match($regexp, $subject, $matches);
    }
    if ($res === FALSE) {
      $this->error("Error in the pattern");
    }
    else if ($res !== 0 && array_key_exists($return, $matches)) {
      $result = $matches[$return];
    }
    return $result;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'regexp' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
      'variant' => [ '_type' => 'enum', '_required' => FALSE, '_values' => ['all'] ],
      'subject' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
      'return' => [ '_type' => 'number', '_required' => FALSE ],
    ];
  }

  static function defaultConfig($data = NULL) {
    return [
        'return' => 1,
      ] + parent::defaultConfig($data);
  }
}
