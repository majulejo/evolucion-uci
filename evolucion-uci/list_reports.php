<?php
// list_reports.php
date_default_timezone_set('Europe/Madrid');
session_start();
header('Content-Type: application/json; charset=utf-8');

if (! isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false, 'message'=>'No autenticado']);
    exit;
}

try {
    // Ajusta el DSN / usuario / clave a tu entorno
    $pdo = new PDO(
      'mysql:host=localhost;dbname=u724879249_evolucion_uci;charset=utf8mb4',
      'u724879249_jamarquez06',
      'Farolill01.'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("
        SELECT
          id,
          box,
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
        ORDER BY `timestamp` DESC
    ");
    $stmt->execute([':uid'=>$_SESSION['user_id']]);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success'=>true, 'reports'=>$reports]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
      'success'=>false,
      'message'=>'Error interno de BBDD: '.$e->getMessage()
    ]);
}
