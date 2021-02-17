<?php

namespace DataMincerPlugins\Fields;

use DirectoryIterator;
use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface path
 * @property PluginFieldInterface each
 * @property string mask
 */
class OpenDir extends PluginFieldBase {

  protected static $pluginId = 'opendir';

  function getValue($data) {
    $path = $this->_fileManager->resolveUri($this->path->value($data));
    if (!file_exists($path)) {
      $this->error("Path not found: $path");
    }
    $mask = $this->resolveParam($data, $this->mask);
    $iterator = new DirectoryIterator($path);
    $result = [];
    foreach ($iterator as $fileinfo) {
      if($fileinfo->isDot()) continue;
      if (fnmatch($mask, $fileinfo->getFilename())) {
        $result[] = [
          'path' => $path,
          'filename' => $fileinfo->getFilename(),
          'extension' => $fileinfo->getExtension(),
          'basename' => $fileinfo->getBasename('.' . $fileinfo->getExtension()),
        ];
      }
    }
    return $result;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'path' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
      'mask' => [ '_type' => 'text', '_required' => FALSE ],
    ];
  }

  static function defaultConfig($data = NULL) {
    return [
      'mask' => '*',
    ] + parent::defaultConfig($data);
  }


}
