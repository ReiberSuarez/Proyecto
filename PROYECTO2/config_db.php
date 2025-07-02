<?php
$host = 'localhost';
$db   = 'cdi';
$user = 'root'; // Cambia si tienes otro usuario MySQL
$pass = '';     // Pon tu contraseña de MySQL aquí si tienes

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>