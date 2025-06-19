<?php
date_default_timezone_set('Europe/Madrid');

session_start();
require_once 'conexion.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Credentials: true');

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Método no permitido']));
}

// Obtener datos JSON
$data = json_decode(file_get_contents('php://input'), true);
$usuario = $data['usuario'] ?? '';
$clave = $data['clave'] ?? '';

if (empty($usuario) || empty($clave)) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Campos vacíos']));
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=u724879249_evolucion_uci;charset=utf8mb4", 
                  'u724879249_jamarquez06', 'Farolill01.');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT id, clave FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($datos && password_verify($clave, $datos['clave'])) {
        $_SESSION['user_id'] = $datos['id'];
        $_SESSION['usuario'] = $usuario;
        session_regenerate_id(true); // Protección contra fixation
        
        error_log("Login exitoso para usuario: $usuario"); // Log para depuración
        
        echo json_encode([
            'success' => true,
            'user_id' => $datos['id'],
            'redirect' => 'app.php' // Agregar redirección explícita
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Usuario o contraseña incorrectos.'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error de base de datos: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión BD'
    ]);
}
?>