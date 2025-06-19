<?php
date_default_timezone_set('Europe/Madrid');
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
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
        SELECT id, box, datos, timestamp
        FROM reports
        WHERE id = ? AND user_id = ?
        LIMIT 1
    ");
    
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    // En get_report.php, verifica el formato de los datos
$informe = $stmt->fetch(PDO::FETCH_ASSOC);
if ($informe) {
    $datos = json_decode($informe['datos'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Intenta reparar datos corruptos
        $datos = json_decode(stripslashes($informe['datos']), true);
    }
    
    echo json_encode([
        'success' => true,
        'id' => $informe['id'],
        'box' => $informe['box'],
        'datos' => $datos ?: []
    ]);
}
    
} catch (PDOException $e) {
    error_log("Error en get_report.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos'
    ]);
}
?>