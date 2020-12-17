<?php

namespace app\views\components\forms;

use edustef\mvcFrame\Component;
use edustef\mvcFrame\Model;

class FormField extends Component
{
  public const TYPE_TEXT = 'text';
  public const TYPE_PASSWORD = 'password';
  public const TYPE_EMAIL = 'email';
  public const TYPE_COLOR = 'color';
  public const TYPE_DATE = 'date';

  private Model $model;
  private string $name;
  private string $labelName;
  private string $type;

  public function __construct(Model $model, string $name, string $type = self::TYPE_TEXT)
  {
    $this->model = $model;
    $this->name = $name;
    $this->labelName = $model->getLabel($name);
    $this->type = $type;
  }

  public function render(): string
  {
    $isInvalidClass = $this->model->hasErrors($this->name) ? 'is-invalid' : '';
    return '
      <div class="flex flex-col mb-3">
          <label for="' . $this->name . '" class="">' . $this->labelName . '</label>
          <input type="' . $this->type . '" class="block border border-grey-light w-full p-3 rounded mb-1" name="' . $this->name . '" placeholder="' . $this->labelName . '" />
          <small class="text-red-600 inline-block h-4">' . $this->model->getFirstError($this->name) . '</small>
      </div>
    ';
  }
}
