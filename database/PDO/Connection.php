<?php

namespace Database\PDO;

class Connection
{
  private static $instance;
  private $connection;

  private function __construct()
  {
    $this->makeConnection();
  }

  public static function getInstance()
  {
    if (!self::$instance instanceof self) {
      self::$instance =  new self;
    }
    return self::$instance;
  }

  public function getConnection()
  {
    return $this->connection;
  }

  private function makeConnection()
  {
    $this->connection = $this->conectarpdo();
  }

  private function conectarpdo()
  {
    $server = "localhost";
    $db = "TiendaScripts";
    $user = "root";
    $password = "caminante";

    try {
      $pdo = new \PDO("mysql:host=$server;dbname=$db", $user, $password, [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_EMULATE_PREPARES => false
      ]);
      return $pdo;
    } catch (\PDOException $e) {
      echo "error al conectar con la db: " . $e->getMessage();
      die();
    }
  }
}
