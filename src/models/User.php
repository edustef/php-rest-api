<?php

namespace app\models;

use edustef\mvcFrame\DatabaseModel;

class User extends DatabaseModel
{
  public string $email = '';
  public string $password = '';
  public string $passwordConfirm = '';

  public function rules(): array
  {
    return [
      'email' => [self::RULE_REQUIRED, self::RULE_EMAIL, [self::RULE_UNIQUE, 'class' => self::class]],
      'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 24]],
      'passwordConfirm' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']]
    ];
  }

  public function attributes(): array
  {
    return [
      'email' => ['label' => 'Email'],
      'password' => ['label' => 'Password'],
      'passwordConfirm' => ['isSaved' => false, 'label' => 'PasswordConfirm']
    ];
  }

  public function save()
  {
    $this->password = password_hash($this->password, PASSWORD_DEFAULT);
    return parent::save();
  }

  public static function tableName(): string
  {
    return 'User';
  }

  public function primaryKey(): string
  {
    return 'id';
  }
}
