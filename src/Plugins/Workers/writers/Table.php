<?php

namespace DataMincerPlugins\Workers\writers;

use Doctrine\DBAL\Driver\Statement;
use DataMincerCore\Exception\PluginException;
use DataMincerCore\Plugin\PluginWorkerBase;
use DataMincerPlugins\Services\Database;

/**
 * @property string table
 * @property string key
 */
class Table extends PluginWorkerBase {

  protected static $pluginId = 'table';
  /**
   * @var Database
   */
  protected $db;
  protected $schema;

  /**
   * @throws PluginException
   */
  function setUp() {
    /** @var Database $db */
    /** @noinspection PhpUndefinedFieldInspection */
    $this->db = $this->services['database'];
    $this->db->createDatabaseSchema();

    // Check table
    if (!array_key_exists($this->table, $this->db->_config['schema'])) {
      $this->error("Table {$this->table} not found in the db schema.");
    }
    $this->schema = $this->db->_config['schema'][$this->table];
    // Check key
    if (!array_key_exists($this->key, $this->schema['columns'])) {
      $this->error("Column for the key {$this->key} not found in the db schema.");
    }
  }


  /**
   * @param $values
   * @return Statement|int
   * @throws PluginException
   */
  function save($values) {
    if (!array_key_exists($this->key, $values)) {
      $this->error("Key {$this->key} not found in the process data.");
    }

    // Check existing row
    $count = $this->db->getConnection()->createQueryBuilder()
      ->select('count(*)')
      ->from($this->table)
      ->where($this->key . ' = ?')
      ->setParameter(0, $values[$this->key])
      ->execute()
      ->fetchColumn();

    if ($count > 0) {
      // Update
      $query = $this->db->getConnection()->createQueryBuilder()->update($this->table);
      $index = 0;
      foreach (array_keys($this->schema['columns']) as $column_name) {
        if (array_key_exists($column_name, $values)) {
          if ($column_name != $this->key) {
            $query->set($column_name, '?');
            $query->setParameter($index++, $values[$column_name]);
          }
          else {
            $query->where($this->key . ' = ?');
            $query->setParameter($index++, $values[$this->key]);
          }
        }
      }
      $result = $query->execute();
    }
    else {
      // Insert
      $query = $this->db->getConnection()->createQueryBuilder()->insert($this->table);
      $index = 0;
      foreach (array_keys($this->schema['columns']) as $column_name) {
        if (array_key_exists($column_name, $values)) {
          $query->setValue($column_name, '?');
          $query->setParameter($index++, $values[$column_name]);
        }
      }
      $result = $query->execute();
    }

    return $result;
  }

  public function generate1($values, $data) {
    yield $data;
  }

  protected function getTableColumns() {

  }

  static function getSchemaChildren() {
    return [
      'service' => [ '_type' => 'text', '_required' => TRUE ],
      'table' =>   [ '_type' => 'text', '_required' => TRUE ],
      'key' =>     [ '_type' => 'text', '_required' => TRUE ],
    ];
  }

}
