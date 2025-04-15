<?php
class Pago
{
  private $id;
  private $usuario_id;
  private $solicitud_id;
  private $monto;
  private $estado;
  private $fecha_pago;

  public function __construct($id = null, $usuario_id = null, $solicitud_id = null, $monto = 0.00, $estado = 'pendiente', $fecha_pago = null)
  {
    $this->id = $id;
    $this->usuario_id = $usuario_id;
    $this->solicitud_id = $solicitud_id;
    $this->monto = $monto;
    $this->estado = $estado;
    $this->fecha_pago = $fecha_pago;
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

  public function getSolicitudId()
  {
    return $this->solicitud_id;
  }
  public function setSolicitudId($solicitud_id)
  {
    $this->solicitud_id = $solicitud_id;
  }

  public function getMonto()
  {
    return $this->monto;
  }
  public function setMonto($monto)
  {
    $this->monto = $monto;
  }

  public function getEstado()
  {
    return $this->estado;
  }
  public function setEstado($estado)
  {
    $this->estado = $estado;
  }

  public function getFechaPago()
  {
    return $this->fecha_pago;
  }
  public function setFechaPago($fecha_pago)
  {
    $this->fecha_pago = $fecha_pago;
  }
}
