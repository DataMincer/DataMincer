<?php

namespace TaskRunner;

use Psr\Log\LoggerInterface;

abstract class Task implements TaskInterface {
  protected static string $taskId;
  protected static bool $versionCheck = true;

  protected LoggerInterface $logger;

  protected array $options;
  protected App $app;

  public static function taskId(): string {
    return static::$taskId;
  }

  /** @noinspection PhpUnused */
  public static function requiresVersionCheck(): bool {
    return static::$versionCheck;
  }

  /** @noinspection PhpUnused */
  public function __construct(App $app, $options) {
    $this->app = $app;
    $this->options = $options;
    $this->logger = $app->logger();
  }
}
