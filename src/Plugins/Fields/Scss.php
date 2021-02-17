<?php

namespace DataMincerPlugins\Fields;

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Exception\CompilerException;
use DataMincerCore\Plugin\PluginFieldBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property PluginFieldInterface template
 * @property PluginFieldInterface[] params
 * @property string|null formatter
 */
class Scss extends PluginFieldBase {

  protected static $pluginId = 'scss';

  protected $formatters = [
    'expanded' => 'Leafo\ScssPhp\Formatter\Expanded',
    'nested' =>  'Leafo\ScssPhp\Formatter\Nested',
    'compressed' =>  'Leafo\ScssPhp\Formatter\Compressed',
    'compact' =>  'Leafo\ScssPhp\Formatter\Compact',
    'crunched' =>  'Leafo\ScssPhp\Formatter\Crunched',
  ];

  /**
   * @inheritDoc
   */
  function getValue($data) {
    $result = NULL;
    $template = $this->template->value($data);
    $scss = new Compiler();
    try {
      $context = $data;
      if (array_key_exists('params', $this->_config)) {
        $params = [];
        foreach ($this->params as $name => $param) {
          $params[$name] = $param->value($data);
        }
        $context['params'] = $params;
      }
      if (!empty($this->formatter)) {
        $formatter = $this->resolveParam($data, $this->formatter);
        $scss->setFormatter(array_key_exists($formatter, $this->formatters) ? $this->formatters[$formatter] : 'UnknownFormatter');
      }
      if (!empty($this->path)) {
        $path = $this->path->value($data);
        if (!file_exists($path)) {
          $this->error('Path not found: ' . $path);
        }
        $scss->addImportPath($path);
      }
      $scss->setVariables($this->prepareVars($context));
      $result = $scss->compile($template);
    } /** @noinspection PhpRedundantCatchClauseInspection */
    catch (CompilerException $e) {
      $this->error($e->getMessage() . "\nTemplate:\n" . $template);
    }
    return $result;
  }

  protected function prepareVars($vars) {
    $result = [];
    foreach ($vars as $key => $value) {
      if (is_array($value)) {
        $res = $this->prepareVars($value);
        foreach ($res as $key2 => $value2) {
          $result[$key . '_' . $key2] = $value2;
        }
      }
      else if (!is_object($value)) {
        $result[$key] = $value;
      }
    }
    return $result;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'template' => [ '_type' => 'partial', '_required' => TRUE, '_partial' => 'field' ],
      'path' => [ '_type' => 'partial', '_required' => FALSE, '_partial' => 'field' ],
      'params' =>   [ '_type' => 'prototype', '_required' => FALSE, '_min_items' => 1, '_prototype' => [
        '_type' => 'partial', '_required' => TRUE, '_partial' => 'field'
        ]],
      'formatter' =>   [ '_type' => 'text', '_required' => FALSE ],
    ];
  }

  static function defaultConfig($data = NULL) {
    return [
      'formatter' => 'expanded',
    ] + parent::defaultConfig($data);
  }

}
