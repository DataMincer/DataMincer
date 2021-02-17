<?php

namespace DataMincerCore;

use YamlCE;

class Bundle {
  protected string $name;
  protected array $extraData;
  protected YamlCE\Bundle $yceBundle;
  public function __construct($name, $data, $filters, $overrides = [], $extra_data = []) {
    $this->name = $name;
    $this->extraData = $extra_data;
    $this->yceBundle = new YamlCE\Bundle($data, $filters, $overrides);
  }

  public function name(): string {
    return $this->name;
  }

  public function getExtraData(): array {
    return $this->extraData;
  }
}
