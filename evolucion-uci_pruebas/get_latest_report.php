<?php
// get_latest_report.php
date_default_timezone_set('Europe/Madrid');
session_start();
header('Content-Type: application/json; charset=utf-8');

if (! isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false, 'message'=>'No autenticado']);
    exit;
}

$box = intval($_GET['box'] ?? 0);
if ($box < 1 || $box > 12) {
    echo json_encode(['success'=>false, 'message'=>'Box inválido']);
    exit;
}

try {
    // Ajusta el DSN / usuario / clave a tu entorno
    $pdo = new PDO(
      'mysql:host=localhost;dbname=u724879249_pruebas;charset=utf8mb4',
      'u724879249_pruebas',
      'Farolill01.'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT
          id,
          datos     AS datos_json,
          DATE_FORMAT(
            CONVERT_TZ(`timestamp`, '+00:00', '+02:00'),
            '%Y-%m-%d'
          ) AS fecha,
          DATE_FORMAT(
            CONVERT_TZ(`timestamp`, '+00:00', '+02:00'),
            '%H:%i:%s'
          ) AS hora
        FROM reports
        WHERE user_id = :uid
          AND box = :box
        ORDER BY `timestamp` DESC
        LIMIT 1
    ");
    $stmt->execute([
      ':uid' => $_SESSION['user_id'],
      ':box' => $box
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (! $row) {
        echo json_encode(['success'=>false, 'message'=>'No hay informes previos']);
        exit;
    }

    echo json_encode([
      'success' => true,
      'id'      => $row['id'],
      'box'     => $box,
      'datos'   => json_decode($row['datos_json'], true),
      'fecha'   => $row['fecha'],
      'hora'    => $row['hora']
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
      'success'=>false,
      'message'=>'Error interno de BBDD: '.$e->getMessage()
    ]);
}
