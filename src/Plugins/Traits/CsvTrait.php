<?php

namespace DataMincerPlugins\Traits;

use DataMincerCore\Exception\PluginException;
use League\Csv\Exception;
use League\Csv\Reader;

trait CsvTrait {

  // Used to autofill sparse data
  protected $lastRowBuffer = [];

  /**
   * @inheritDoc
   * @throws PluginException
   */
  public function getRecords($config, $data) {
    $columns_config = count($config['columns']) ? $config['columns'] : NULL;
    foreach ($this->getReader($config)->getRecords() as $record) {
      if (!$columns_config) {
        $columns_config = array_combine(array_keys($record), array_keys($record));
      }
      $row = [];
      foreach ($this->readColumns($columns_config) as $field_name => $column_info) {
        if (array_key_exists($column_info['name'], $record)) {
          $value = $record[$column_info['name']];
          if (empty($value)) {
            if ($column_info['autofill']) {
              // Use the last value
              $value = $this->lastRowBuffer[$field_name] ?? $column_info['default'];
            }
            else {
              $value = $column_info['default'];
            }
          }
          $row[$field_name] = $value;
        }
        else {
          /** @noinspection PhpUndefinedMethodInspection */
          $this->error('Column not found in CSV: ' . $column_info['name']);
        }
      }
      $this->lastRowBuffer = $row;
      yield $row;
    }
  }

  protected function readColumns($columns) {
    return array_map(function ($col) {
      return is_scalar($col) ?
        ['name' => $col] + $this->defaultColumnDefinition() :
        $col + $this->defaultColumnDefinition();
    }, $columns);
  }

  protected function defaultColumnDefinition() {
    return [
      'autofill' => FALSE,
      'default' => '',
    ];
  }

  /** @noinspection PhpDocRedundantThrowsInspection */
  /**
   * Get the CSV reader.
   *
   * @param $config
   * @return Reader
   *   The reader.
   * @throws PluginException
   */
  protected function getReader($config) {
    $reader = Reader::createFromString($config['data']);
    try {
      $reader->setDelimiter($config['delimiter']);
      $reader->setEnclosure($config['enclosure']);
      $reader->setEscape($config['escape']);
      $reader->setHeaderOffset($config['header_offset']);
    } catch (Exception $e) {
      $this->error($e->getMessage());
    }
    return $reader;
  }

  static function getSchemaChildren() {
    return parent::getSchemaChildren() + [
        'data' => ['_type' => 'partial', '_required' => TRUE, '_partial' => 'field'],
        'header_offset' => ['_type' => 'number', '_required' => FALSE],
        'delimiter' => ['_type' => 'text', '_required' => FALSE],
        'enclosure' => ['_type' => 'text', '_required' => FALSE],
        'escape' => ['_type' => 'text', '_required' => FALSE],
        'columns' => ['_type' => 'choice', '_required' => TRUE, '_choices' => [
          'field' => ['_type' => 'partial', '_partial' => 'field'],
          'list' => ['_type' => 'prototype', '_required' => TRUE, '_prototype' => [
            '_type' => 'choice', '_required' => TRUE, '_choices' => [
              'name' => ['_type' => 'text', '_required' => TRUE],
              'struct' => ['_type' => 'array', '_required' => TRUE, '_children' => [
                'name' => ['_type' => 'text', '_required' => TRUE],
                'autofill' => ['_type' => 'boolean', '_required' => FALSE],
                'default' => ['_type' => 'text', '_required' => FALSE],
              ]],
            ],
          ]],
        ]],
      ];
  }

  static function defaultConfig($data = NULL) {
    return parent::defaultConfig($data) + [
        'header_offset' => 0,
        'columns' => [],
        'delimiter' => ",",
        'enclosure' => "\"",
        'escape' => "\\",
      ];
  }

}
