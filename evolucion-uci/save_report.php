<?php
date_default_timezone_set('Europe/Madrid');
session_start();
header('Content-Type: application/json; charset=utf-8');

// 1) Verificar sesión
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

// 2) Leer y validar datos JSON
$input = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'JSON inválido: ' . json_last_error_msg()]);
    exit;
}

if (!$input || !isset($input['id'], $input['box'], $input['datos'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Validar que el box sea un número válido (1-12)
if (!is_numeric($input['box']) || $input['box'] < 1 || $input['box'] > 12) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Número de Box inválido']);
    exit;
}

// Validar estructura de datos
if (!is_array($input['datos'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Formato de datos incorrecto']);
    exit;
}

// 3) Conexión a base de datos
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=u724879249_evolucion_uci;charset=utf8mb4',
        'u724879249_jamarquez06',
        'Farolill01.',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Convertir datos a JSON con manejo de errores
    $datos_json = json_encode($input['datos'], JSON_UNESCAPED_UNICODE);
    if ($datos_json === false) {
        throw new Exception('Error al codificar datos a JSON');
    }

    // Preparar consulta SQL corregida
    $stmt = $pdo->prepare("
        INSERT INTO reports (id, user_id, box, datos, timestamp)
        VALUES (:id, :user_id, :box, :datos, CONVERT_TZ(NOW(), 'SYSTEM', 'Europe/Madrid'))
        ON DUPLICATE KEY UPDATE
            datos = VALUES(datos),
            timestamp = CONVERT_TZ(NOW(), 'SYSTEM', 'Europe/Madrid')
    ");

    $stmt->execute([
        ':id' => $input['id'],
        ':user_id' => $_SESSION['user_id'],
        ':box' => (int)$input['box'],
        ':datos' => $datos_json
    ]);

    // Verificar si se afectaron filas
    if ($stmt->rowCount() === 0) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'No se pudo guardar el informe']);
        exit;
    }

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'id' => $input['id'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (PDOException $e) {
    error_log("PDO Error en save_report.php: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false, 
        'message' => 'Error de base de datos',
        'error_details' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error en save_report.php: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'success' => false, 
        'message' => 'Error al procesar la solicitud',
        'error_details' => $e->getMessage()
    ]);
}