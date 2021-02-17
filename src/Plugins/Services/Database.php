<?php

namespace DataMincerPlugins\Services;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Table;
use DataMincerCore\Exception\PluginException;
use DataMincerCore\Plugin\PluginFieldInterface;
use DataMincerCore\Plugin\PluginServiceBase;
use DataMincerCore\Exception\DataMincerException;

/**
 * @property mixed|null options
 */
class Database extends PluginServiceBase {

  protected static $pluginId = 'db.doctrine';

  /** @var Connection */
  private $connection;

  protected $db_options = [];

  /**
   * @throws PluginException
   */
  public function initialize() {
    parent::initialize();
    $options = $this->options;
    if (!empty($options['path'])) {
      /** @var PluginFieldInterface $path */
      $path = $options['path'];
      $options['path'] = $path->value();
    }
    $this->db_options = $options;
  }

  /**
   * @return Connection
   */
  public function getConnection() {
    if (empty($this->connection)) {
      try {
        $connection_params = [
            'wrapperClass' => 'DataMincerPlugins\\Services\\Connection',
          ] + $this->db_options;
        $this->connection = DriverManager::getConnection($connection_params);
      }
      catch (DBALException $e) {
        throw new DataMincerException('Connection failed: ' . $e->getMessage());
      }
    }
    return $this->connection;
  }

  /**
   * Creates database tables using definitions from config
   */
  public function createDatabaseSchema() {
    $sm = $this->getConnection()->getSchemaManager();
    try {
      foreach ($this->_config['schema'] ?? [] as $table_name => $table_info) {
        if (!$sm->tablesExist($table_name)) {
          $table = new Table($table_name);
          foreach ($table_info['columns'] as $column_name => $column_info) {
            $column = $table->addColumn($column_name, $column_info['type'], $column_info['options'] ?? []);
            if (!empty($column_info['autoinc']) && $column_info['autoinc']) {
              $column->setAutoincrement(TRUE);
            }
          }
          if (!empty($table_info['primary'])) {
            foreach ($table_info['primary'] as $key_name => $column_list) {
              $table->setPrimaryKey($column_list, $key_name);
            }
          }
          if (!empty($table_info['unique keys'])) {
            foreach ($table_info['unique keys'] as $key_name => $column_list) {
              $table->addUniqueIndex($column_list, $key_name);
            }
          }
          $sm->createTable($table);
        }
      }
    } catch (DBALException $e) {
      throw new DataMincerException("Cannot create database: " . $e->getMessage());
    }
  }

  static function getSchemaChildren() {
    return [
      'options' => [ '_type' => 'array', '_required' => TRUE,  '_ignore_extra_keys' => TRUE, '_children' => [
        'driver' => [ '_type' => 'text', '_required' => TRUE ],
        // @see https://www.doctrine-project.org/projects/doctrine-dbal/en/2.9/reference/configuration.html#connection-details
        // for the list of available options
        'path' => [ '_type' => 'partial', '_required' => FALSE, '_partial' => 'field'],
      ]],
      'schema' => [
        '_type' => 'prototype',
        '_required' => FALSE,
        '_min_items' => 1,
        '_prototype' => [
          '_type' => 'array',
          '_children' => [
            'columns' => [
              '_type' => 'prototype',
              '_required' => TRUE,
              '_min_items' => 1,
              '_prototype' => [
                '_type' => 'array',
                '_children' => [
                  'type' => [
                    '_type' => 'text',
                    '_required' => TRUE,
                  ],
                  'options' => [
                    '_type' => 'array',
                    '_ignore_extra_keys' => TRUE,
                    '_children' => [],
                  ],
                  'autoinc' => [
                    '_type' => 'boolean',
                  ],
                ],
              ],
            ],
            'primary' => [
              '_type' => 'prototype',
              '_min_items' => 1,
              '_prototype' => [
                '_type' => 'prototype',
                '_min_items' => 1,
                '_prototype' => [
                  '_type' => 'text'
                ]
              ],
            ],
            'unique keys' => [
              '_type' => 'prototype',
              '_min_items' => 1,
              '_prototype' => [
                '_type' => 'prototype',
                '_min_items' => 1,
                '_prototype' => [
                  '_type' => 'text'
                ]
              ],
            ],
          ],
        ],
      ],
    ];
  }

}
