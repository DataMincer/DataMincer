<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface params_string
 * @property PluginFieldInterface in
 * @property PluginFieldInterface out
 * @property string imagemagick
 * @property bool cache
 */
class ImageMagick extends PluginFieldBase {

  protected static $pluginId = 'imagemagick';

  const CACHE_BIN = 'imagemagick';

  function getValue($data) {
    $params_string = $this->params_string->value($data);
    $use_cache = $this->resolveParam($data, $this->cache);
    $in = $this->_fileManager->resolveUri($this->in->value($data));
    if (!file_exists($in)) {
      $this->error("File not found: " . $in);
    }
    $cid = $in . ':' . $params_string;
    if ($use_cache
      && $this->cacheManager->exists($cid, self::CACHE_BIN)
      && ($file = $this->cacheManager->getFile($cid, self::CACHE_BIN)) &&
      $file !== FALSE) {
      return $file;
    }
    $out = $this->_fileManager->resolveUri($this->out->value($data));
    $imagemagick = $this->resolveParam($data, $this->imagemagick);
    $cmd = escapeshellcmd($imagemagick . ' ' . $in . ' ' . $params_string . ' ' . $out) . ' 2>&1';
    $result = exec($cmd, $output, $return);
    if ($return !== 0) {
      $this->error("ImageMagick error: " . $result . "\nCommand was: " . $cmd);
    }
    if ($use_cache) {
      $this->cacheManager->setFile($cid, $out, self::CACHE_BIN);
    }
    return $out;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'params_string' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field'],
      // Input file path
      'in' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field'],
      // Output file path
      'out' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field'],
      'imagemagick' => [ '_type' => 'text', '_required' => FALSE ],
      'cache' => [ '_type' => 'boolean', '_required' => FALSE ],
    ];
  }

  static function defaultConfig($data = NULL) {
    return [
        'imagemagick' => '/usr/bin/convert',
        'cache' => TRUE,
      ] + parent::defaultConfig($data);
  }

}
