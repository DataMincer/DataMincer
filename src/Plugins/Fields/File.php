<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface path
 * @property string format
 */
class File extends PluginFieldBase {

  protected static $pluginId = 'file';

  function getValue($data) {
    $path = $this->_fileManager->resolveUri($this->path->value($data));
    $format = $this->resolveParams($data, $this->format);
    if ($this->_fileManager->isLocal($path) && !file_exists($path)) {
      $this->error("Cannot open file: '$path'");
    }
    $data = @file_get_contents($path);
    if ($data === FALSE) {
      $this->error('Cannot read file data: ' . error_get_last()['message']);
    }
    $result = NULL;
    switch ($format) {
      case 'json':
        /** @noinspection PhpComposerExtensionStubsInspection */
        $result = json_decode($data, TRUE);
        if (is_null($result)) {
          $this->error('Cannot decode JSON string');
        }
        break;
      case 'none':
        $result = $data;
        break;
      default:
        $this->error("Unknown file format: '$format'");
    }
    return $result;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'path' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
      'format' => [ '_type' => 'enum', '_required' => FALSE, '_values' => ['none', 'json'] ],
    ];
  }

  static function defaultConfig($data = NULL) {
    return [
      'format' => 'none',
    ] + parent::defaultConfig($data);
  }


}
