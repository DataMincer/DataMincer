<?php

namespace DataMincerPlugins\Services;

use /** @noinspection PhpComposerExtensionStubsInspection */ finfo;
use DataMincerCore\Exception\PluginException;
use DataMincerCore\Plugin\PluginServiceBase;

class MediaFetcher extends PluginServiceBase {

  protected static $pluginId = 'mediafetcher';

  const CACHE_BIN = 'mediafetcher';

  protected $client;

  /**
   * @param $url
   * @return array|mixed|null
   * @throws PluginException
   */
  function fetch($url) {
    $use_cache = boolval($this->_config['cache'] ?? FALSE);
    $cid = $request_id = sha1($url);
    $data = NULL;
    if ($use_cache
      && $this->cacheManager->exists($cid, self::CACHE_BIN)
      && ($data = $this->cacheManager->getData($cid, self::CACHE_BIN)) &&
      $data !== FALSE) {
      return $data;
    }
    $contents = @file_get_contents($url);
    if ($contents === FALSE) {
      $this->error('Cannot fetch file: ' . $url);
    }
    $data = [
      'request_id' => $request_id,
      'data' => $contents,
      'mime' => $this->getMimeFromData($contents),
    ];
    if ($use_cache) {
      /** @noinspection PhpUndefinedVariableInspection */
      $this->cacheManager->setData($cid, $data, self::CACHE_BIN);
    }
    return $data;
  }

  function getMimeFromData($data) {
    /** @noinspection PhpComposerExtensionStubsInspection */
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    return $finfo->buffer($data);
  }

  static function defaultConfig($data = NULL) {
    return [
      'cache' => FALSE,
    ];
  }

  static function getSchemaChildren() {
    return [
      'cache' => [ '_type' => 'boolean', '_required' => FALSE ],
    ];

  }

}
