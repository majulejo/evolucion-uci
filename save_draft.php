<?php
// save_draft.php
date_default_timezone_set('Europe/Madrid');
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['success'=>false,'message'=>'No autenticado']);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$box   = intval($input['box'] ?? 0);
$datos = $input['datos'] ?? null;

if ($box < 1 || $box > 12 || !is_array($datos)) {
  http_response_code(400);
  echo json_encode(['success'=>false,'message'=>'Parámetros inválidos']);
  exit;
}

try {
  $pdo = new PDO(
    'mysql:host=localhost;dbname=u724879249_evolucion_uci;charset=utf8mb4',
    'u724879249_jamarquez06',
    'Farolill01.'
  );
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $pdo->prepare("
    INSERT INTO drafts (user_id, box, datos_json)
    VALUES (:uid, :box, :json)
    ON DUPLICATE KEY UPDATE datos_json = :json
  ");
  $json = json_encode($datos, JSON_UNESCAPED_UNICODE);
  $stmt->execute([
    ':uid'  => $_SESSION['user_id'],
    ':box'  => $box,
    ':json' => $json
  ]);

  echo json_encode(['success'=>true]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>'Error de BBDD: '.$e->getMessage()]);
}
