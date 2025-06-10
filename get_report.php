<?php
date_default_timezone_set('Europe/Madrid');
session_start();
// …

header('Content-Type: application/json; charset=utf-8');

// 1) Validaciones básicas
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success'=>false,'message'=>'No autenticado']);
    exit;
}
if (empty($_GET['id'])) {
    echo json_encode(['success'=>false,'message'=>'Falta el parámetro id']);
    exit;
}
$id  = $_GET['id'];
$uid = $_SESSION['user_id'];

// 2) Conexión PDO (ajusta tus credenciales)
try {
    $db = new PDO('mysql:host=localhost;dbname=u724879249_evolucion_uci;charset=utf8mb4', 'u724879249_jamarquez06', 'Farolill01.');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ————————————————
    // 3) Aquí corregimos el SELECT:
    $stmt = $db->prepare("
        SELECT box, datos 
          FROM reports 
         WHERE id = :id 
           AND user_id = :uid
    ");
    $stmt->execute([':id'=>$id, ':uid'=>$uid]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo json_encode(['success'=>false,'message'=>'Informe no encontrado']);
        exit;
    }

    // 4) Decodificamos el JSON que tienes en la columna 'datos'
    $datos = json_decode($row['datos'], true);

    // 5) Devolvemos la respuesta correcta
    echo json_encode([
        'success'=> true,
        'id'     => $id,
        'box'    => (int)$row['box'],
        'datos'  => $datos
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno de BBDD: ' . $e->getMessage()
    ]);
    exit;
}
