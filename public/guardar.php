<?php
// Recibir datos JSON del frontend
$data = json_decode(file_get_contents("php://input"), true);
//dd($data);
if (!$data) {
  http_response_code(400);
  echo "Datos inválidos.";
  exit;
}

// Crear carpeta del script (sanitizar nombre)
$titulo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data["titulo"]);
$dir = __DIR__ . "/scripts/$titulo";

if (!is_dir($dir)) {
  mkdir($dir, 0777, true);
}

// Guardar el script .sh
file_put_contents("$dir/script.sh", $data["contenido"]);

// Guardar los metadatos en JSON
$info = [
  "titulo" => $data["titulo"],
  "descripcion" => $data["descripcion"],
  "categoria" => $data["categoria"],
  "precio" => (float) $data["precio"],
  "etiquetas" => explode(",", $data["etiquetas"]),
  "ultima_modificacion" => date("Y-m-d H:i:s")
];

file_put_contents("$dir/info.json", json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Script guardado correctamente en /scripts/$titulo/";
