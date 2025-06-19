<?php
date_default_timezone_set('Europe/Madrid');
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=u724879249_evolucion_uci;charset=utf8mb4',
        'u724879249_jamarquez06',
        'Farolill01.'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Establecer zona horaria para esta conexión
    $pdo->exec("SET time_zone = '+02:00'");
    
    $stmt = $pdo->prepare("
        SELECT 
            id, 
            box, 
            DATE_FORMAT(timestamp, '%d/%m/%Y') as fecha,
            DATE_FORMAT(timestamp, '%H:%i') as hora,
            timestamp as fecha_original
        FROM reports
        WHERE user_id = ?
        ORDER BY timestamp DESC
        LIMIT 50
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    $informes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formateo adicional en PHP para asegurar consistencia
    foreach ($informes as &$informe) {
        try {
            $dt = new DateTime($informe['fecha_original']);
            $dt->setTimezone(new DateTimeZone('Europe/Madrid'));
            $informe['fecha'] = $dt->format('d/m/Y');
            $informe['hora'] = $dt->format('H:i');
        } catch (Exception $e) {
            // Si falla, mantener los valores de la consulta SQL
            error_log("Error formateando fecha: " . $e->getMessage());
        }
    }
    
    echo json_encode([
        'success' => true,
        'reports' => $informes
    ]);
    
} catch (PDOException $e) {
    error_log("Error en list_reports.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos'
    ]);
}
?>