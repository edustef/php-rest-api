<?php

namespace edustef\mvcFrame;

abstract class Component
{
  abstract public function render(): string;

  public function __toString()
  {
    return $this->render();
  }
}
