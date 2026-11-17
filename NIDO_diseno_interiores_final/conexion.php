<?php
// Conexión a la base de datos usando PDO.
$host = 'localhost';
$db   = 'nido_diseno';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $conexion = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    // No mostramos datos internos de la conexión al usuario.
    exit('No se pudo conectar con la base de datos. Revisá la configuración de conexion.php.');
}
