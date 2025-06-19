<?php
// delete_reports.php

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
if (!$input || empty($input['id'])) {
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Falta el parámetro id']);
    exit;
}

$id  = $input['id'];
$uid = $_SESSION['user_id'];

try {
    // 3) Conexión PDO
    $pdo = new PDO(
      'mysql:host=localhost;dbname=u724879249_evolucion_uci;charset=utf8mb4',
      'u724879249_jamarquez06',
      'Farolill01.'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4) DELETE
    $stmt = $pdo->prepare("
        DELETE FROM reports
         WHERE id = :id
           AND user_id = :uid
    ");
    $stmt->execute([
      ':id'  => $id,
      ':uid' => $uid
    ]);

    // 5) ¿Se borró realmente alguna fila?
    if ($stmt->rowCount() === 0) {
        echo json_encode([
          'success'=>false,
          'message'=>'Informe no encontrado o no permitido'
        ]);
    } else {
        echo json_encode([
          'success'=>true,
          'message'=>'Informe eliminado'
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
      'success'=>false,
      'message'=>'Error de BBDD: '.$e->getMessage()
    ]);
}
