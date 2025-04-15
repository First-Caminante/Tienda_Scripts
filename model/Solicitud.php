<?php
class Solicitud
{
  private $id;
  private $usuario_id;
  private $titulo;
  private $descripcion;
  private $estado;
  private $fecha_creacion;

  public function __construct($id = null, $usuario_id = null, $titulo = null, $descripcion = null, $estado = 'pendiente', $fecha_creacion = null)
  {
    $this->id = $id;
    $this->usuario_id = $usuario_id;
    $this->titulo = $titulo;
    $this->descripcion = $descripcion;
    $this->estado = $estado;
    $this->fecha_creacion = $fecha_creacion;
  }

  public function getId()
  {
    return $this->id;
  }
  public function setId($id)
  {
    $this->id = $id;
  }

  public function getUsuarioId()
  {
    return $this->usuario_id;
  }
  public function setUsuarioId($usuario_id)
  {
    $this->usuario_id = $usuario_id;
  }

  public function getTitulo()
  {
    return $this->titulo;
  }
  public function setTitulo($titulo)
  {
    $this->titulo = $titulo;
  }

  public function getDescripcion()
  {
    return $this->descripcion;
  }
  public function setDescripcion($descripcion)
  {
    $this->descripcion = $descripcion;
  }

  public function getEstado()
  {
    return $this->estado;
  }
  public function setEstado($estado)
  {
    $this->estado = $estado;
  }

  public function getFechaCreacion()
  {
    return $this->fecha_creacion;
  }
  public function setFechaCreacion($fecha_creacion)
  {
    $this->fecha_creacion = $fecha_creacion;
  }
}
