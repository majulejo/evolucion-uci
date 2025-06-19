<?php
session_start();
date_default_timezone_set('Europe/Madrid');
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Credentials: true');


if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'authenticated' => true,
        'user_id' => $_SESSION['user_id']
    ]);
} else {
    echo json_encode(['authenticated' => false]);
}
?>