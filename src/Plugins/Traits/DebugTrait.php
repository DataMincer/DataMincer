<?php

namespace DataMincerPlugins\Traits;

trait DebugTrait {

  protected static $MAXLENGTH = 100;

  protected function prepareData($data, $shrink = FALSE) {
    $result = [];
    if (!is_array($data)) {
      $data = [$data];
    }
    foreach($data as $key => $value) {
      switch(gettype($value)) {
        case "string":
          if ($this->isBase64($value)) {
            if ($shrink && strlen($value) > static::$MAXLENGTH) {
              $value = substr($value, 0, static::$MAXLENGTH) . '...';
            }
          }
          if ($this->isBinary($value)) {
            $value = '[BINARY]';
          }
          $result[$key] = $value;
          break;
        case "array":
          $result[$key] = $this->prepareData($value, $shrink);
          break;
        case "object":
          $result[$key] = (string) $value;
          break;
        default:
          $result[$key] = $value;
      }
    }
    return $result;
  }

  function isBase64($data) {
    return preg_match('~^[a-zA-Z0-9/+]*={0,2}$~', $data);
  }

  function isBinary($data) {
    if (!mb_check_encoding($data, 'UTF-8')) {
      return preg_match('~[^\x20-\x7E\t\r\n]~', $data) > 0;
    }
    return FALSE;
  }

  public static function defaultConfig($data = NULL) {
    /** @noinspection PhpUndefinedClassInspection */
    return parent::defaultConfig($data) + [
      'dense' => TRUE
    ];
  }

  static function getSchemaChildren() {
    /** @noinspection PhpUndefinedClassInspection */
    return parent::getSchemaChildren() + [
      'dense' => ['_type' => 'boolean', '_required' => FALSE],
      'select' => ['_type' => 'partial', '_required' => FALSE, '_partial' => 'field']
    ];
  }



}
