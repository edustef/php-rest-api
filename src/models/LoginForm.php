<?php

namespace app\models;

use edustef\mvcFrame\Application;
use edustef\mvcFrame\Model;
use app\models\User;

class LoginForm extends Model
{
  public string $email = '';
  public string $password = '';

  public function rules(): array
  {
    return [
      'email' => [self::RULE_REQUIRED, self::RULE_EMAIL],
      'password' => [self::RULE_REQUIRED]
    ];
  }

  public function attributes(): array
  {
    return [
      'email' => ['isSaved' => true, 'label' => 'Email'], 
      'password' => ['isSaved' => true, 'label' => 'Password']
    ];
  }

  public function login()
  {
    $user = User::findOne(['email' => $this->email]);
    if (!$user) {
      $this->addErrorMessage('email', 'User does not exist with this email address');
      return false;
    }
    if (!password_verify($this->password, $user->password)) {
      $this->addErrorMessage('password', 'Password incorrect!');
      return false;
    }

    return Application::$app->login($user);
  }
}
