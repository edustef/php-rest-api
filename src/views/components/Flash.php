<?php

namespace app\views\components;

use edustef\mvcFrame\Component;

class Flash extends Component
{
  private string $color;
  private string $message;

  public function __construct($message, $color)
  {
    $this->message = $message;
    $this->color = $color;
  }

  public function render(): string
  {
    return '
      <div class="p-4 my-4 bg-' . $this->color . '-200 shadow-md font-semibold rounded-md">
        ' . $this->message . '
      </div>
    ';
  }
}
