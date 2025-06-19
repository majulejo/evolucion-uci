<?php
session_start();
require_once 'conexion.php';

header('Content-Type: application/json');

/* -------- 1) Datos recibidos -------- */
$usuario = $_POST['usuario'] ?? '';
$clave   = $_POST['clave']   ?? '';

if ($usuario === '' || $clave === '') {
  exit( json_encode([
    'success'=>false,
    'message'=>'Campos vacíos'
  ]));
}

/* -------- 2) Conexión MySQL -------- */
$mysqli = new mysqli(
  'localhost',        // host
  'u724879249_pruebas',      // <-- usuario MySQL
  'Farolill01.',      // <-- contraseña MySQL
  'u724879249_pruebas' // base
);
if ($mysqli->connect_errno) {
  exit( json_encode([
    'success'=>false,
    'message'=>'Error de conexión BD'
  ]));
}

/* -------- 3) Consulta preparada -------- */
$stmt = $mysqli->prepare(
  'SELECT id, usuario, clave, user_id
     FROM usuarios
    WHERE usuario = ? LIMIT 1'
);
$stmt->bind_param('s', $usuario);
$stmt->execute();
$datos = $stmt->get_result()->fetch_assoc();

/* -------- 4) Verificación -------- */
if ( $datos && password_verify($clave, $datos['clave']) ) {
  $_SESSION['user_id'] = $datos['user_id'];   // o $datos['id']
  echo json_encode([
    'success' => true,
    'user_id' => $_SESSION['user_id']
  ]);
} else {
  echo json_encode([
    'success' => false,
    'message' => 'Usuario o contraseña incorrectos.'
  ]);
}
