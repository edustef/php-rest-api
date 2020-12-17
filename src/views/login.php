<?php

/**
 * @var edustef\mvcFrame\Model $model
 */

use app\views\components\FormField;

?>

<h1>Login page</h1>
<form action="" method="POST">
  <?= new FormField($model, 'email', FormField::TYPE_EMAIL) ?>
  <?= new FormField($model, 'password', FormField::TYPE_PASSWORD) ?>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>