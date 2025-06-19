<?php
$host = "localhost";
$db = "u724879249_pruebas";
$user = "u724879249_pruebas";
$pass = "Farolill01.";

$db = new mysqli($host,$user,$pass,$db);
if ($db->connect_error) {
    throw new Exception('Error MySQL: '.$db->connect_error);
}
