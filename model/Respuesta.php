<?php
class Respuesta
{
  private $id;
  private $solicitud_id;
  private $desarrollador_id;
  private $mensaje;
  private $archivo_script;
  private $fecha_respuesta;

  public function __construct($id = null, $solicitud_id = null, $desarrollador_id = null, $mensaje = null, $archivo_script = null, $fecha_respuesta = null)
  {
    $this->id = $id;
    $this->solicitud_id = $solicitud_id;
    $this->desarrollador_id = $desarrollador_id;
    $this->mensaje = $mensaje;
    $this->archivo_script = $archivo_script;
    $this->fecha_respuesta = $fecha_respuesta;
  }

  public function getId()
  {
    return $this->id;
  }
  public function setId($id)
  {
    $this->id = $id;
  }

  public function getSolicitudId()
  {
    return $this->solicitud_id;
  }
  public function setSolicitudId($solicitud_id)
  {
    $this->solicitud_id = $solicitud_id;
  }

  public function getDesarrolladorId()
  {
    return $this->desarrollador_id;
  }
  public function setDesarrolladorId($desarrollador_id)
  {
    $this->desarrollador_id = $desarrollador_id;
  }

  public function getMensaje()
  {
    return $this->mensaje;
  }
  public function setMensaje($mensaje)
  {
    $this->mensaje = $mensaje;
  }

  public function getArchivoScript()
  {
    return $this->archivo_script;
  }
  public function setArchivoScript($archivo_script)
  {
    $this->archivo_script = $archivo_script;
  }

  public function getFechaRespuesta()
  {
    return $this->fecha_respuesta;
  }
  public function setFechaRespuesta($fecha_respuesta)
  {
    $this->fecha_respuesta = $fecha_respuesta;
  }
}
