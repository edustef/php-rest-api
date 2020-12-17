<?php

namespace edustef\mvcFrame;

abstract class DatabaseModel extends Model
{
  abstract public static function tableName(): string;
  abstract public function primaryKey(): string;

  public function save()
  {
    $tableName = static::tableName();
    $attributes = array_filter($this->attributes(), function ($attr) {
      if (!isset($attr['isSaved'])) {
        $attr['isSaved'] = true;
      }
      return $attr['isSaved'] === true;
    });

    $attributes = array_keys($attributes);

    $params = array_map(fn ($attr) => ':' . $attr,  $attributes);

    $stmnt = self::prepare('INSERT INTO ' . $tableName . ' (' . implode(',', $attributes) . ') VALUES (' . implode(',', $params) . ')');
    foreach ($attributes as $attribute) {
      $stmnt->bindValue(':' . $attribute, $this->{$attribute});
    }

    $stmnt->execute();
    return true;
  }

  public static function findOne(array $where)
  {
    $tableName = static::tableName();
    $attributes = array_keys($where);
    $whereQuery = implode(' AND ', array_map(fn ($attr) => "$attr = :$attr", $attributes));

    $stmnt = self::prepare("SELECT * FROM $tableName WHERE " . $whereQuery);
    foreach ($where as $key => $value) {
      $stmnt->bindValue(":$key", $value);
    }

    $stmnt->execute();
    return $stmnt->fetchObject(static::class);
  }

  public static function findAll(array $where)
  {
    $tableName = static::tableName();
    $attributes = array_keys($where);
    $whereQuery = implode(' AND ', array_map(fn ($attr) => "$attr = :$attr", $attributes));

    $stmnt = self::prepare("SELECT * FROM $tableName WHERE " . $whereQuery);
    foreach ($where as $key => $value) {
      $stmnt->bindValue(":$key", $value);
    }

    $stmnt->execute();
    return $stmnt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, static::class);
  }

  public static function getAll()
  {
    $tableName = static::tableName();
    $stmnt = self::prepare("SELECT * FROM $tableName");
    $stmnt->execute();

    return $stmnt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, static::class);
  }

  public static function delete(array $where)
  {
    $tableName = static::tableName();
    $attributes = array_keys($where);
    $whereQuery = implode(' AND ', array_map(fn ($attr) => "$attr = :$attr", $attributes));

    $stmnt = self::prepare("DELETE FROM $tableName WHERE " . $whereQuery);
    foreach ($where as $key => $value) {
      $stmnt->bindValue(":$key", $value);
    }

    $stmnt->execute();
    return $stmnt->fetchObject(static::class);
  }

  public function update(array $where)
  {
    $tableName = static::tableName();
    $attributes = array_filter($this->attributes(), function ($attr) {
      if (!isset($attr['isSaved'])) {
        $attr['isSaved'] = true;
      }
      return $attr['isSaved'] === true;
    });

    $attributes = array_keys($attributes);

    $whereQuery = implode(' AND ', array_map(fn ($attr) => "$attr = :$attr", $attributes));

    $params = array_map(fn ($attr) => ':' . $attr,  $attributes);

    $stmnt = self::prepare('INSERT INTO ' . $tableName . ' (' . implode(',', $attributes) . ') VALUES (' . implode(',', $params) . ')');
    foreach ($attributes as $attribute) {
      $stmnt->bindValue(':' . $attribute, $this->{$attribute});
    }

    $stmnt->execute();
    return true;
  }

  public static function prepare(string $mysql)
  {
    return Application::$app->database->pdo->prepare($mysql);
  }
}
