<?php

namespace Model;


class Usuario
{
  private $id;
  private $nombre;
  private $email;
  private $password_hash;
  private $rol;
  private $fecha_registro;

  public function __construct($id = null, $nombre = null, $email = null, $password_hash = null, $rol = 'cliente', $fecha_registro = null)
  {
    $this->id = $id;
    $this->nombre = $nombre;
    $this->email = $email;
    $this->password_hash = $password_hash;
    $this->rol = $rol;
    $this->fecha_registro = $fecha_registro;
  }

  public function getId()
  {
    return $this->id;
  }
  public function setId($id)
  {
    $this->id = $id;
  }

  public function getNombre()
  {
    return $this->nombre;
  }
  public function setNombre($nombre)
  {
    $this->nombre = $nombre;
  }

  public function getEmail()
  {
    return $this->email;
  }
  public function setEmail($email)
  {
    $this->email = $email;
  }

  public function getPasswordHash()
  {
    return $this->password_hash;
  }
  public function setPasswordHash($password_hash)
  {
    $this->password_hash = $password_hash;
  }

  public function getRol()
  {
    return $this->rol;
  }
  public function setRol($rol)
  {
    $this->rol = $rol;
  }

  public function getFechaRegistro()
  {
    return $this->fecha_registro;
  }
  public function setFechaRegistro($fecha_registro)
  {
    $this->fecha_registro = $fecha_registro;
  }
}
