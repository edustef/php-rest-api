<?php

namespace app\views\components\forms;

use app\views\components\Button;
use edustef\mvcFrame\Component;

class Form extends Component
{
  public const METHOD_POST = 'POST';
  public const METHOD_GET = 'GET';
  private array $children;

  public function __construct(array $children, string $method = self::METHOD_GET)
  {
    $this->children = $children;
    $this->method = $method;
  }
  public function render(): string
  {
    return '
      <form action="" method="POST">
        ' . implode('', $this->children) . '
      </form>
    ';
  }
}
