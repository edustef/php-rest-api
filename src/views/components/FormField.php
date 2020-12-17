<?php

namespace app\views\components;

use edustef\mvcFrame\Component;
use edustef\mvcFrame\Model;

class FormField extends Component
{
  public const TYPE_TEXT = 'text';
  public const TYPE_PASSWORD = 'password';
  public const TYPE_EMAIL = 'email';
  public const TYPE_COLOR = 'color';
  public const TYPE_DATE = 'date';

  public Model $model;
  public string $name;
  public string $labelName;
  public string $type = self::TYPE_TEXT;

  public function __construct(Model $model, $name, $type)
  {
    $this->model = $model;
    $this->name = $name;
    $this->labelName = $this->model->getLabel($name);
    $this->type = $type;
  }

  public function render(): string
  {
    $isInvalidClass = $this->model->hasErrors($this->name) ? 'is-invalid' : '';
    return '
      <div class="form-group">
        <label for="' . $this->name . '">' . $this->labelName . '</label>
        <input name="' . $this->name . '" value="' . $this->model->{$this->name} . '" class="form-control ' . $isInvalidClass . '" type="' . $this->type . '">
        <small style="display:inline-block;height:1rem" class="text-danger">' . $this->model->getFirstError($this->name) . '</small>
      </div>
    ';
  }
}
