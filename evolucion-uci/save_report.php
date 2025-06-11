<?php
date_default_timezone_set('Europe/Madrid');
session_start();
header('Content-Type: application/json; charset=utf-8');

// 1) Comprobar sesión
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'No autenticado']);
  exit;
}

// 2) Leer payload
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['id'], $input['box'], $input['datos'])) {
  echo json_encode(['success' => false, 'message' => 'Payload inválido']);
  exit;
}

$id = $input['id'];
$box = intval($input['box']);
$datos = json_encode($input['datos'], JSON_UNESCAPED_UNICODE);

// 3) Guardar en BBDD
try {
  $pdo = new PDO(
    'mysql:host=localhost;dbname=u724879249_evolucion_uci;charset=utf8mb4',
    'u724879249_jamarquez06',
    'Farolill01.'
  );
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Configurar la zona horaria para la sesión actual
  $pdo->exec("SET time_zone = '+02:00'");

  $stmt = $pdo->prepare("
    INSERT INTO reports
      (id, user_id, box, datos, `timestamp`)
    VALUES
      (:id, :uid, :box, :datos, NOW())
    ON DUPLICATE KEY UPDATE
      datos     = VALUES(datos),
      `timestamp` = NOW()
  ");
  $stmt->execute([
    ':id' => $id,
    ':uid' => $_SESSION['user_id'],
    ':box' => $box,
    ':datos' => $datos
  ]);

  echo json_encode([
    'success' => true,
    'id' => $id
  ]);

} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'message' => 'Error interno de BBDD: ' . $e->getMessage()
  ]);
}
?>