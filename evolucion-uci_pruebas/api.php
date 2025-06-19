<?php
header("Content-Type: application/json");
session_start();

// ——————————————————————————————————————————————
// 1) Configuración de la base de datos (ajusta si usas otro fichero de config)
// ——————————————————————————————————————————————
$host = "localhost";
$db   = "u724879249_pruebas";
$user = "u724879249_pruebas";
$pass = "Farolill01.";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error de conexión a la base de datos: " . $e->getMessage()
    ]);
    exit;
}

// ——————————————————————————————————————————————
// 2) Leemos el JSON enviado por POST
// ——————————————————————————————————————————————
$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data) || !isset($data['action'])) {
    echo json_encode([
        "success" => false,
        "message" => "Petición mal formada o falta 'action'."
    ]);
    exit;
}

// ——————————————————————————————————————————————
// 3) Por ahora forzamos userId=1 durante la depuración.
//    Más adelante, reemplázalo por $_SESSION['userId'] tras implementar login.
// ——————————————————————————————————————————————
$userId = 1;

// ——————————————————————————————————————————————
// 4) Discriminamos la acción
// ——————————————————————————————————————————————
switch ($data['action']) {
    case 'save':
        saveData($pdo, $userId, $data);
        break;

    case 'load':
        loadData($pdo, $userId, $data);
        break;

    case 'deleteAll':
        deleteAll($pdo, $userId, $data);
        break;

    case 'deleteIngresos':
        deleteIngresos($pdo, $userId, $data);
        break;

    case 'deletePerdidas':
        deletePerdidas($pdo, $userId, $data);
        break;

    default:
        echo json_encode([
            "success" => false,
            "message" => "Acción desconocida: " . $data['action']
        ]);
        break;
}
exit;

////////////////////////////////////////////////////////////////////////////////
//                             FUNCIONES INTERNAS                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * saveData:
 *   Inserta o actualiza con ON DUPLICATE KEY UPDATE los campos de datos_balance.
 *   Se espera que $data tenga:
 *     - $data['boxNumber']
 *     - $data['data'] = array asociativo: campo => valor
 */
function saveData(PDO $pdo, int $userId, array $data) {
    if (!isset($data['boxNumber']) || !isset($data['data']) || !is_array($data['data'])) {
        echo json_encode([
            "success" => false,
            "message" => "Parámetros insuficientes para guardar datos"
        ]);
        return;
    }

    $boxNumber = (int) $data['boxNumber'];
    $campos    = $data['data']; // Ej: ["peso_box"=>"70", "horas_desde_ingreso_box"=>"5", …]

    // Construimos dinámicamente las columnas, placeholders y la parte de UPDATE
    $columnas      = [];
    $placeholders  = [];
    $updates       = [];
    $valoresParams = [
        ':usuario_id' => $userId,
        ':box_number' => $boxNumber
    ];

    $i = 0;
    foreach ($campos as $col => $val) {
        // IMPORTANTE: $col debe coincidir EXACTAMENTE con el nombre de la columna en la tabla MySQL
        $param           = ":v{$i}";
        $columnas[]      = "`$col`";
        $placeholders[]  = $param;
        $updates[]       = "`$col` = VALUES(`$col`)";
        $valoresParams[$param] = $val;
        $i++;
    }

    if (empty($columnas)) {
        echo json_encode([
            "success" => false,
            "message" => "No hay datos para guardar"
        ]);
        return;
    }

    $colList   = implode(", ", $columnas);
    $valList   = implode(", ", $placeholders);
    $updateStr = implode(", ", $updates);

    $sql = "
        INSERT INTO datos_balance (
            usuario_id,
            box_number,
            $colList
        ) VALUES (
            :usuario_id,
            :box_number,
            $valList
        )
        ON DUPLICATE KEY UPDATE
            $updateStr
    ";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valoresParams);
        echo json_encode([ "success" => true ]);
    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error al guardar los datos: " . $e->getMessage()
        ]);
    }
}

/**
 * loadData:
 *   Recupera todos los campos de datos_balance para (usuario_id, box_number).
 *   Devuelve el JSON de la fila o [] si no existe.
 *   Se espera que $data tenga:
 *     - $data['boxNumber']
 */
function loadData(PDO $pdo, int $userId, array $data) {
    if (!isset($data['boxNumber'])) {
        echo json_encode([
            "success" => false,
            "message" => "Parámetros insuficientes para cargar datos"
        ]);
        return;
    }

    $boxNumber = (int) $data['boxNumber'];
    $stmt      = $pdo->prepare("
        SELECT *
        FROM datos_balance
        WHERE usuario_id = :uid
          AND box_number = :box
        LIMIT 1
    ");
    $stmt->execute([
        ':uid' => $userId,
        ':box' => $boxNumber
    ]);

    $row = $stmt->fetch();
    if ($row) {
        echo json_encode($row);
    } else {
        echo json_encode([]);
    }
}

/**
 * deleteAll:
 *   Elimina la fila entera de datos_balance para (usuario_id, box_number).
 *   Se espera que $data tenga:
 *     - $data['boxNumber']
 */
function deleteAll(PDO $pdo, int $userId, array $data) {
    if (!isset($data['boxNumber'])) {
        echo json_encode([
            "success" => false,
            "message" => "Parámetros insuficientes para borrar todos los datos"
        ]);
        return;
    }

    $boxNumber = (int) $data['boxNumber'];
    $stmt      = $pdo->prepare("
        DELETE FROM datos_balance
        WHERE usuario_id = :uid
          AND box_number = :box
    ");
    try {
        $stmt->execute([
            ':uid' => $userId,
            ':box' => $boxNumber
        ]);
        echo json_encode([ "success" => true ]);
    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error al borrar todos los datos: " . $e->getMessage()
        ]);
    }
}

/**
 * deleteIngresos:
 *   Pone NULL en todas las columnas relacionadas con ingresos
 *   para (usuario_id, box_number).
 *   Se espera que $data tenga:
 *     - $data['boxNumber']
 */
function deleteIngresos(PDO $pdo, int $userId, array $data) {
    if (!isset($data['boxNumber'])) {
        echo json_encode([
            "success" => false,
            "message" => "Parámetros insuficientes para borrar ingresos"
        ]);
        return;
    }

    $boxNumber = (int) $data['boxNumber'];
    $camposIngresos = [
        "ingreso_midazolam_box",
        "ingreso_fentanest_box",
        "ingreso_propofol_box",
        "ingreso_remifentanilo_box",
        "ingreso_dexdor_box",
        "ingreso_noradrenalina_box",
        "ingreso_insulina_box",
        "ingreso_sueroterapia1_box",
        "ingreso_sueroterapia2_box",
        "ingreso_sueroterapia3_box",
        "ingreso_medicacion_box",
        "ingreso_sangreplasma_box",
        "ingreso_agua_endogena_box",
        "ingreso_oral_box",
        "ingreso_enteral_box",
        "ingreso_parenteral_box",
        "resumen_total_ingresos_box"
    ];
    $setParts = [];
    foreach ($camposIngresos as $f) {
        $setParts[] = "`$f` = NULL";
    }
    $setClause = implode(", ", $setParts);

    $sql = "
        UPDATE datos_balance
        SET $setClause
        WHERE usuario_id = :uid
          AND box_number = :box
    ";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uid' => $userId,
            ':box' => $boxNumber
        ]);
        echo json_encode([ "success" => true ]);
    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error al borrar los ingresos: " . $e->getMessage()
        ]);
    }
}

/**
 * deletePerdidas:
 *   Pone NULL en todas las columnas relacionadas con pérdidas
 *   para (usuario_id, box_number).
 *   Se espera que $data tenga:
 *     - $data['boxNumber']
 */
function deletePerdidas(PDO $pdo, int $userId, array $data) {
    if (!isset($data['boxNumber'])) {
        echo json_encode([
            "success" => false,
            "message" => "Parámetros insuficientes para borrar pérdidas"
        ]);
        return;
    }

    $boxNumber = (int) $data['boxNumber'];
    $camposPerdidas = [
        "perdida_orina_box",
        "perdida_vomitos_box",
        "fiebre37_horas_box",
        "fiebre37_calculo_box",
        "fiebre38_horas_box",
        "fiebre38_calculo_box",
        "fiebre39_horas_box",
        "fiebre39_calculo_box",
        "rpm25_horas_box",
        "rpm25_calculo_box",
        "rpm35_horas_box",
        "rpm35_calculo_box",
        "perdida_sng_box",
        "perdida_hdfvvc_box",
        "perdida_drenajes_box",
        "perdidas_insensibles_box",
        "perdida_fuerafluidos_box",
        "total_perdidas_box"
    ];
    $setParts = [];
    foreach ($camposPerdidas as $f) {
        $setParts[] = "`$f` = NULL";
    }
    $setClause = implode(", ", $setParts);

    $sql = "
        UPDATE datos_balance
        SET $setClause
        WHERE usuario_id = :uid
          AND box_number = :box
    ";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uid' => $userId,
            ':box' => $boxNumber
        ]);
        echo json_encode([ "success" => true ]);
    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error al borrar las pérdidas: " . $e->getMessage()
        ]);
    }
}
?>
