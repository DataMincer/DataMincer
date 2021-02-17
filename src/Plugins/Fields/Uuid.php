<?php

namespace DataMincerPlugins\Fields;

use Exception;
use Ramsey\Uuid\Uuid as UuidBase;
use DataMincerCore\Plugin\PluginFieldBase;

class Uuid extends PluginFieldBase {

  protected static $pluginId = 'uuid';

  function getValue($data) {
    $result = NULL;
    try {
      $result = (string) UuidBase::uuid1();
    } catch (Exception $e) {
      $this->error($e->getMessage());
    }
    return $result;
  }

}
