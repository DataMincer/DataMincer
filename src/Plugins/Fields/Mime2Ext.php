<?php

namespace DataMincerPlugins\Fields;

use Mimey\MimeTypes;
use DataMincerCore\Plugin\PluginFieldBase;

/**
 * @property string|null mime
 */
class Mime2Ext extends PluginFieldBase {

  protected static $pluginId = 'mime2ext';

  /**
   * @var MimeTypes
   */
  protected $mimes;

  public function initialize() {
    $this->mimes = new MimeTypes;
    parent::initialize();
  }

  function getValue($data) {
    $mime = $this->resolveParam($data, $this->mime);
    $result = $this->mimes->getExtension($mime);
    return $result ?? 'bin';
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'mime' => [ '_type' => 'text', '_required' => TRUE ]
    ];
  }

}
