<?php

namespace DataMincerPlugins\Fields;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\ChainLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;
use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface template
 * @property PluginFieldInterface[] params
 * @property array options
 */
class Twig extends PluginFieldBase {

  protected static $pluginId = 'twig';

  /** @var Environment  */
  protected $twig = NULL;
  protected $loaders = [];

  public function initialize() {
    parent::initialize();
    /** @var  $loaders */
    $this->loaders = ['array' => new ArrayLoader(), 'file' => new FilesystemLoader()];
    $this->loaders['file']->addPath($this->_fileManager->resolveUri('bundle://'), 'bundle');
    $tmp_path = $this->_fileManager->resolveUri('tmp://');
    if (!is_dir($tmp_path)) {
      $this->_fileManager->prepareDirectory($tmp_path);
    }
    $this->loaders['file']->addPath($tmp_path, 'tmp');
    $twig = new Environment(new ChainLoader($this->loaders), $this->options ?? []);
    $twig->addFunction(new TwigFunction('bp', function ($context) {
      if (function_exists('xdebug_break')) {
        /** @noinspection PhpComposerExtensionStubsInspection */
        xdebug_break();
      }
    }, [
      'needs_context' => TRUE,
    ]));
    $twig->addFilter(new TwigFilter('key', function ($array) {
      return key($array);
    }));
    $this->twig = $twig;
  }

  /**
   * @inheritDoc
   */
  function getValue($data) {
    $template = $this->template->value($data);
    $this->loaders['array']->setTemplate('template', $template);
    $result = NULL;
    try {
      $context = $data;
      if (array_key_exists('params', $this->_config)) {
        $params = [];
        foreach ($this->params as $name => $param) {
          $params[$name] = $param->value($data);
        }
        //$context['params'] = $this->resolveParams($data, $this->params);
        $context['params'] = $params;
      }
      $result = $this->twig->load('template')->render($context);
    }
    catch (LoaderError | RuntimeError | SyntaxError $e) {
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
        'cache' => [ '_type' => 'text', '_required' => FALSE ],
        'debug' => [ '_type' => 'boolean', '_required' => FALSE ],
      ]]
    ];
  }

}
