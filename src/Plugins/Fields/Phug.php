<?php

namespace DataMincerPlugins\Fields;

use DataMincerCore\Exception\PluginException;
use JsPhpize\JsPhpizePhug;
use Phug\Renderer;
use Phug\RendererException;
use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface template
 * @property PluginFieldInterface[] params
 * @property array options
 */
class Phug extends PluginFieldBase {

  protected static $pluginId = 'pug';

  /** @var Renderer */
  protected $phug;

  /**
   * @throws PluginException
   */
  public function initialize() {
    parent::initialize();
    try {
      $opts = $this->options;
      $opts['modules'] = [JsPhpizePhug::class];
      $opts['path'] = [$this->_fileManager->resolveUri('bundle://')];
      $this->phug = new Renderer($opts);
    }
    catch (RendererException $e) {
      $this->error("Cannot initialize Phug: " . $e->getMessage());
    }
  }

  /**
   * @inheritDoc
   */
  function getValue($data) {
    $template = $this->template->value($data);
    $result = NULL;
    try {
      $context = $data;
      if (array_key_exists('params', $this->_config)) {
        $params = [];
        foreach ($this->params as $name => $param) {
          $params[$name] = $param->value($data);
        }
        $context['params'] = $params;
      }
      $result = $this->phug->render($template, $context);
    }
    catch (RendererException $e) {
      $this->error($e->getMessage() . "\nTemplate:\n" . $template);
    }
    return $result;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'template' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
      'params' =>   [ '_type' => 'prototype', '_required' => FALSE, '_min_items' => 1, '_prototype' => [
        '_type' => 'partial', '_required' => TRUE, '_partial' => 'field'
      ]],
      'options' => [ '_type' => 'array', '_required' => FALSE, '_ignore_extra_keys' => TRUE, '_children' => [
        'cache_dir' => [ '_type' => 'boolean', '_required' => FALSE ],
        'debug' => [ '_type' => 'boolean', '_required' => FALSE ],
        'doctype' => [ '_type' => 'text', '_required' => FALSE ],
        'pretty' => [ '_type' => 'boolean', '_required' => FALSE ],
        // TODO: add filters?
        'filename' => [ '_type' => 'text', '_required' => FALSE ],
        'extensions' => [ '_type' => 'prototype', '_required' => FALSE, '_min_items' => 1, '_prototype' => [
          '_type' => 'text', '_required' => FALSE
        ]],
      ]]
    ];
  }

  static function defaultConfig($data = NULL) {
    return [
      'options' => [
        'cache' => FALSE,
        'debug' => TRUE,
        'pretty' => TRUE,
        'extensions' => ['.pug']
      ],
    ] + parent::defaultConfig($data);
  }

}
