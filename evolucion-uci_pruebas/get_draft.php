<?php
date_default_timezone_set('Europe/Madrid');
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['success' => false, 'message' => 'No autenticado']);
  exit;
}

$box = intval($_GET['box'] ?? 0);

if ($box < 1 || $box > 12) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
  exit;
}

try {
  $pdo = new PDO(
    'mysql:host=localhost;dbname=u724879249_pruebas;charset=utf8mb4',
    'u724879249_pruebas',
    'Farolill01.'
  );
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Configurar la zona horaria para la sesión actual
  $pdo->exec("SET time_zone = '+02:00'");

  $stmt = $pdo->prepare("
    SELECT datos_json
    FROM drafts
    WHERE user_id = :uid AND box = :box
  ");
  $stmt->execute([
    ':uid' => $_SESSION['user_id'],
    ':box' => $box
  ]);

  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row && isset($row['datos_json'])) {
    $datos = json_decode($row['datos_json'], true);
    echo json_encode(['success' => true, 'datos' => $datos]);
  } else {
    echo json_encode(['success' => true, 'datos' => null]);
  }
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Error de BBDD: ' . $e->getMessage()]);
}
?>