<?php

/**
 * @var $model \app\models\User
 */

use app\views\components\FormField;

?>

<h1>Register page</h1>
<form action="" method="POST">
  <?= new FormField($model, 'email', FormField::TYPE_EMAIL) ?>
  <?= new FormField($model, 'password', FormField::TYPE_PASSWORD) ?>
  <?= new FormField($model, 'passwordConfirm', FormField::TYPE_PASSWORD) ?>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>