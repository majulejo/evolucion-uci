<?php
/**
 * admin.php – Panel para gestionar la tabla `usuarios`
 * -----------------------------------------------------------------------------
 * ▸ Acceso restringido con token único (faroladmin2024)
 * ▸ Tabla mínima: id (AI‑PK), usuario (UNIQUE), clave (hash)
 * ▸ Sesión caduca tras 3 min de inactividad
 * ▸ Bootstrap 5 (CDN) para UI responsive
 * -----------------------------------------------------------------------------
 * Requisitos: PHP ≥ 7.4 con extensión mysqli habilitada
 * -----------------------------------------------------------------------------
 */

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ───────────────────────── CABECERAS NO‑CACHE ─────────────────────────
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// ───────────────────────── CONFIGURACIÓN ──────────────────────────────
$ADMIN_TOKEN      = 'faroladmin2024';
$SESSION_TIMEOUT  = 180;

$DB_HOST = 'localhost';
$DB_NAME = 'u724879249_pruebas';
$DB_USER = 'u724879249_pruebas';
$DB_PASS = 'Farolill01.';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die('Conexión fallida: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

function login_form(string $msg = ''): void {
    ?>
    <!doctype html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Login admin</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="card shadow p-4" style="max-width:420px;width:100%">
        <h1 class="h4 mb-3 text-center">Panel de administración</h1>
        <?php if ($msg): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <form method="post">
            <label class="form-label" for="token">Introduce tu token</label>
            <input type="password" class="form-control mb-3" id="token" name="token" required autofocus>
            <button class="btn btn-primary w-100">Entrar</button>
        </form>
<script>
function validarFormulario() {
  const u = document.querySelector('[name=nuevo_usuario]').value.trim();
  const p = document.querySelector('[name=nuevo_clave]').value.trim();
  if (!u || !p) {
    alert('Debes rellenar usuario y contraseña.');
    return false;
  }
  return true;
}
</script>

    </div>
    </body>
    </html>
    <?php
    exit;
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if ((time() - ($_SESSION['login_time'] ?? 0)) > $SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        login_form('La sesión ha expirado. Vuelve a introducir el token.');
    }
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
        if ($_POST['token'] === $ADMIN_TOKEN) {
            $_SESSION['logged_in']  = true;
            $_SESSION['login_time'] = time();
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
        login_form('Token incorrecto');
    }
    login_form();
}
$_SESSION['login_time'] = time();

$alert = '';
$alertType = 'success';
$highlightId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
    case 'create':
    $usuario = trim($_POST['nuevo_usuario'] ?? '');
    $pass    = trim($_POST['nuevo_clave'] ?? '');

    // Log para depuración
    error_log("Intentando crear usuario: [$usuario]");

    if ($usuario === '' || $pass === '') {
        $alertType = 'danger';
        $alert = 'Debes rellenar usuario y contraseña.';
        break;
    }

    // Verificar si ya existe
    $check = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = ?");
    $check->bind_param("s", $usuario);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        $alertType = 'danger';
        $alert = "El usuario <strong>$usuario</strong> ya existe.";
        break;
    }

    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $user_id = uniqid('user_', true);

    $stmt = $conn->prepare('INSERT INTO usuarios (usuario, clave, user_id) VALUES (?, ?, ?)');
    if (!$stmt) {
        $alertType = 'danger';
        $alert = 'Error al preparar consulta: ' . $conn->error;
        break;
    }
    $stmt->bind_param('sss', $usuario, $hash, $user_id);
    if ($stmt->execute()) {
        $highlightId = $conn->insert_id;
        $alert = "Usuario creado correctamente: <strong>$usuario</strong> | contraseña: <strong>$pass</strong>";
    } else {
        $alertType = 'danger';
        $alert = 'Error al crear: ' . $stmt->error;
    }
    $stmt->close();
    break;




        case 'delete':
            $id = (int) ($_POST['id'] ?? 0);
            if ($id) {
                $stmt = $conn->prepare('DELETE FROM usuarios WHERE id = ?');
                $stmt->bind_param('i', $id);
                if ($stmt->execute()) {
                    $alert = 'Usuario eliminado correctamente.';
                } else {
                    $alertType = 'danger';
                    $alert = 'Error al eliminar: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $alertType = 'warning';
                $alert = 'ID no válido.';
            }
            break;
    }
}

$result   = $conn->query('SELECT id, usuario FROM usuarios ORDER BY id DESC');
$usuarios = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Gestión de usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
<?php if ($alert): ?>
    <div class="alert alert-<?= $alertType ?> alert-dismissible fade show" role="alert">
        <?= $alert ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">Crear nuevo usuario</div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="action" value="create">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" class="form-control" name="nuevo_usuario" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="text" class="form-control" name="nuevo_clave" id="nuevo_clave" oninput="actualizarHashPreview()" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Hash generado (previsualización)</label>
                <input type="text" class="form-control text-muted" id="preview_hash" readonly>
            </div>
            <button class="btn btn-success">Crear usuario</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Usuarios existentes</div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr><th>ID</th><th>Usuario</th><th>Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['usuario']) ?></td>
                    <td>
                        <form method="post" style="display:inline">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <input type="hidden" name="action" value="delete">
                            <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
function actualizarHashPreview() {
  const pass = document.getElementById("nuevo_clave").value;
  if (!pass) return;
  fetch("hash_preview.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "pass=" + encodeURIComponent(pass)
  })
  .then(res => res.text())
  .then(hash => document.getElementById("preview_hash").value = hash);
}
</script>

</body>
</html>
