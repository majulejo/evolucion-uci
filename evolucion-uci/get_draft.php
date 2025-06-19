<?php
date_default_timezone_set('Europe/Madrid');
session_start();

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Verificar sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

// 2. Obtener parámetro box
$box = isset($_GET['box']) ? intval($_GET['box']) : 0;
if ($box < 1 || $box > 12) {
    echo json_encode(['success' => false, 'message' => 'Box inválido']);
    exit;
}

// 3. Conectar a la base de datos
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=u724879249_evolucion_uci;charset=utf8mb4',
        'u724879249_jamarquez06',
        'Farolill01.'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4. Buscar borrador
    $stmt = $pdo->prepare("
        SELECT datos 
        FROM drafts 
        WHERE user_id = ? AND box = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id'], $box]);
    $borrador = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($borrador && !empty($borrador['datos'])) {
        // 5. Devolver borrador encontrado
        echo json_encode([
            'success' => true,
            'datos' => json_decode($borrador['datos'], true)
        ]);
    } else {
        // 6. No hay borrador
        echo json_encode([
            'success' => false,
            'message' => 'No se encontró borrador para este box'
        ]);
    }
} catch (PDOException $e) {
    // 7. Manejar errores de base de datos
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
}
?>