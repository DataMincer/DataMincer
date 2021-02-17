<?php

namespace DataMincerPlugins\Workers\processors;

use DataMincerCore\Plugin\PluginBufferingWorkerBase;
use DataMincerCore\Plugin\PluginFieldInterface;

/**
 * @property string[] by
 * @property PluginFieldInterface use
 */
class Group extends PluginBufferingWorkerBase {

  protected static $pluginId = 'group';

  protected $currentGroup = NULL;
  protected $lastGroup = NULL;
  protected $context = [];
  /**
   * @var bool
   */
  private $buffering = TRUE;

  public function isBuffering() {
    return $this->buffering;
  }

  public function evaluate($data = []) {
    // Do not evaluate 'use' field, as it's intended for process()
    return $this->evaluateChildren($data, [], [['use']]);
  }

  /**
   * @inheritDoc
   */
  public function process($config) {
    $data = yield;
    $use = $this->use->value($data);
    $by = $this->by;
    $this->currentGroup = $this->extractGroup($by, $use);
    if (!is_null($this->lastGroup)) {
      if ($this->lastGroup !== $this->currentGroup) {
        $this->buffering = FALSE;
      }
    }
    else {
      $this->lastGroup = $this->currentGroup;
    }
    $row = $this->extractGroupNot($by, $use);
    // Save other values
    $this->context = $this->extractGroupNot([$use], $data);
    yield $row;
  }

  protected function rowsEqual($columns, $r1, $r2) {
    foreach ($columns as $column) {
      if ($r1[$column] !== $r2[$column]) {
        return FALSE;
      }
    }
    return TRUE;
  }

  protected function extractGroup($columns, $r) {
    return array_filter($r, function($i) use ($columns) {
      return in_array($i, $columns);
    }, ARRAY_FILTER_USE_KEY);
  }

  protected function extractGroupNot($columns, $r) {
    return array_filter($r, function($i) use ($columns) {
      return !in_array($i, $columns);
    }, ARRAY_FILTER_USE_KEY);
  }

  public function processBuffer() {
    $row = $this->lastGroup + [
      'items' => $this->buffer,
    ];
    // Reset buffer
    $this->buffer = [];
    $this->lastGroup = $this->currentGroup;
    $this->buffering = TRUE;
    return $this->mergeResult($row, $this->context, $this->getConfig());
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
      'use' => ['_type' => 'partial', '_required' => FALSE, '_partial' => 'field'],
      'by' => ['_type' => 'prototype', '_required' => TRUE, '_min_items' => 1, '_prototype' => [
        '_type' => 'text', '_required' => TRUE
      ]]
    ];
  }

  static function defaultConfig($data = NULL) {
    return parent::defaultConfig($data) + [
      'use' => '@row'
    ];
  }


}
