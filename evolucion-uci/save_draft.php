<?php
date_default_timezone_set('Europe/Madrid');
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['box'], $input['datos'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
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
        INSERT INTO drafts (user_id, box, datos)
        VALUES (:user_id, :box, :datos)
        ON DUPLICATE KEY UPDATE
            datos = VALUES(datos),
            timestamp = NOW()
    ");
    
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':box' => $input['box'],
        ':datos' => json_encode($input['datos'], JSON_UNESCAPED_UNICODE)
    ]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    error_log("Error en save_draft.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al guardar borrador']);
}
?>