<?php

namespace edustef\mvcFrame;

abstract class Model
{
  public const RULE_REQUIRED = 'required';
  public const RULE_EMAIL = 'email';
  public const RULE_MIN = 'min';
  public const RULE_MAX = 'max';
  public const RULE_MATCH = 'match';
  public const RULE_UNIQUE = 'unique';
  public const RULE_NUMERIC = 'numeric';

  abstract public function attributes(): array;

  public array $errors = [];

  public function loadData($data)
  {
    foreach ($data as $key => $value) {
      if (property_exists($this, $key)) {
        $this->{$key} = $value;
      }
    }
  }

  abstract public function rules(): array;

  public function getLabel($attribute)
  {
    return $this->attributes()[$attribute]['label'];
  }

  public function validate(): bool
  {
    foreach ($this->rules() as $attribute => $rules) {
      $value = $this->{$attribute};
      foreach ($rules as $rule) {
        $ruleName = $rule;

        if (is_array($ruleName)) {
          $ruleName = $rule[0];
        }

        if ($ruleName === self::RULE_REQUIRED && !$value) {
          $this->addError($attribute, self::RULE_REQUIRED);
        }

        if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
          $this->addError($attribute, self::RULE_EMAIL);
        }

        if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
          $this->addError($attribute, self::RULE_MIN, $rule);
        }

        if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
          $this->addError($attribute, self::RULE_MAX, $rule);
        }

        if ($ruleName === self::RULE_NUMERIC && !is_numeric($value)) {
          $this->addError($attribute, self::RULE_MAX, $rule);
        }

        if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
          $this->addError($attribute, self::RULE_MATCH, ['match' => $this->getLabel($rule['match'])]);
        }

        if ($ruleName === self::RULE_UNIQUE) {
          $className = $rule['class'];
          $uniqueAttribute = $rule['attribute'] ?? $attribute;
          $tableName = $className::tableName();

          $query = 'SELECT * FROM ' . $tableName . ' WHERE ' . $uniqueAttribute . ' = :attribute';
          $stmnt = Application::$app->database->pdo->prepare($query);
          $stmnt->bindValue(':attribute', $value);
          $stmnt->execute();
          $record = $stmnt->fetchObject();
          if ($record) {
            $this->addError($attribute, self::RULE_UNIQUE, ['unique' => $this->getLabel($attribute)]);
          }
        }
      }
    }

    return empty($this->errors);
  }

  private function addError($attribute, $rule, $params = [])
  {
    $errorMessage = $this->errorMessages($rule);

    foreach ($params as $key => $value) {
      if (!is_numeric($value)) {
        $value = strtolower($value);
      }
      $errorMessage = str_replace('{' . $key . '}', $value, $errorMessage);
    }
    $this->errors[$attribute][] = $errorMessage;
  }

  public function addErrorMessage(string $attribute, string $errorMessage)
  {
    $this->errors[$attribute][] = $errorMessage;
  }

  public function errorMessages($ruleName): string
  {
    $errorMessages = [
      self::RULE_REQUIRED => 'This field is required.',
      self::RULE_EMAIL => 'This field must be a valid email address.',
      self::RULE_MIN => 'Min length of this field must be {min}.',
      self::RULE_MAX => 'Max length of this field must be {max}.',
      self::RULE_MATCH => 'This field must match with {match} field.',
      self::RULE_UNIQUE => 'A record with this {unique} already exists.',
    ];

    return $errorMessages[$ruleName] ?? '';
  }

  public function hasErrors($attribute): bool
  {
    return isset($this->errors[$attribute]);
  }

  public function getFirstError($attribute): string
  {
    return $this->errors[$attribute][0] ?? '';
  }
}
