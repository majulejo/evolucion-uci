<?php
// get_draft.php
date_default_timezone_set('Europe/Madrid');
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['success'=>false,'message'=>'No autenticado']);
  exit;
}

$box = intval($_GET['box'] ?? 0);
if ($box < 1 || $box > 12) {
  http_response_code(400);
  echo json_encode(['success'=>false,'message'=>'Box inválido']);
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
    SELECT datos_json
      FROM drafts
     WHERE user_id = :uid
       AND box     = :box
  ");
  $stmt->execute([
    ':uid' => $_SESSION['user_id'],
    ':box' => $box
  ]);

  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    echo json_encode([
      'success' => true,
      'datos'   => json_decode($row['datos_json'], true)
    ]);
  } else {
    echo json_encode(['success'=>false,'message'=>'No hay borrador']);
  }
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>'Error de BBDD: '.$e->getMessage()]);
}
