<?php
$host = "localhost";
$db = "u724879249_pruebas";
$user = "u724879249_pruebas";
$pass = "Farolill01.";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Conexión fallida: " . $conn->connect_error);
}
?>
