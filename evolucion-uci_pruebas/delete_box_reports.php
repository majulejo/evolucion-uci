<?php
// delete_box_reports.php
date_default_timezone_set('Europe/Madrid');
session_start();
header('Content-Type: application/json; charset=utf-8');

// 1) Autenticación
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'message'=>'No autenticado']);
    exit;
}

// 2) Leer payload
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['box'])) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Falta el parámetro box']);
    exit;
}

$box = intval($input['box']);
if ($box < 1 || $box > 12) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Box inválido']);
    exit;
}

$uid = $_SESSION['user_id'];

try {
    // 3) Conexión PDO
    $pdo = new PDO(
      'mysql:host=localhost;dbname=u724879249_pruebas;charset=utf8mb4',
      'u724879249_pruebas',
      'Farolill01.'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4) DELETE múltiple
    $stmt = $pdo->prepare("
        DELETE FROM reports
         WHERE box = :box
           AND user_id = :uid
    ");
    $stmt->execute([
      ':box' => $box,
      ':uid' => $uid
    ]);

    // 5) Comprobamos cuántas filas se borraron
    $deleted = $stmt->rowCount();
    if ($deleted === 0) {
        echo json_encode([
          'success'=>false,
          'message'=>"No había informes para Box {$box}"
        ]);
    } else {
        echo json_encode([
          'success'=>true,
          'message'=>"{$deleted} informe(s) del Box {$box} eliminados"
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
      'success'=>false,
      'message'=>'Error de BBDD: '.$e->getMessage()
    ]);
}
