<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;
use DataMincerCore\Timer;
use DataMincerPlugins\Services\MediaFetcher;

/**
 * @property PluginFieldInterface url
 * @property array|null requestOptions
 */
class FetchMedia extends PluginFieldBase {

  protected static $pluginId = 'fetchmedia';

  protected static $pluginDeps = [
    [
      'type' => 'service',
      'name' => 'mediafetcher'
    ]
  ];

  function getValue($data) {
    $url = $this->url->value($data);
    if (empty($url)) {
      $this->error('Url is empty');
    }
    /** @var MediaFetcher $media_fetcher */
    $media_fetcher = $this->getDefaultDependency('service');
    $_probe_name = "MEDIA FETCH";
    Timer::begin($_probe_name);
    $result = $media_fetcher->fetch($url);
    Timer::end($_probe_name);
    return $result;
  }

  static function getSchemaChildren() {
    return [
      'url' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field']
    ];
  }

}
