<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface contents
 * @property PluginFieldInterface destination
 * @property boolean base64
 * @property boolean write_seen
 */
class SaveFile extends PluginFieldBase {

  protected static $pluginId = 'savefile';
  protected $seenFiles = [];

  function getValue($data) {
    $contents = $this->contents->value($data);
    if ($this->base64) {
      $contents = base64_decode($contents);
      // Don't resolve param if it's a binary content
    }
    $destination_path = $this->_fileManager->resolveUri($this->destination->value($data));
    if ($this->write_seen && in_array($destination_path, $this->seenFiles)) {
      // Skip written earlier
      return $destination_path;
    }
    $destination_dir = dirname($destination_path);
    $this->_fileManager->prepareDirectory($destination_dir);
    file_put_contents($destination_path, $contents);
    return $destination_path;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'contents' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
      'base64' => [ '_type' => 'boolean', '_required' => FALSE ],
      'destination' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
      'write_seen' => [ '_type' => 'boolean', '_required' => FALSE ],
    ];
  }

  static function defaultConfig($data = NULL) {
    return [
      'base64' => FALSE,
      'write_seen' => FALSE,
    ] + parent::defaultConfig($data);
  }

}
