<?php
// Captura automática de variables de entorno de infraestructura
$host = getenv('DB_HOST') ?: 'dpg-xxxxxx-a.oregon-postgres.render.com';
$db   = getenv('DB_NAME') ?: 'unilago_reviews';
$user = getenv('DB_USER') ?: 'unilago_admin';
$pass = getenv('DB_PASS') ?: 'TuPasswordDeRenderPostgres';
$port = getenv('DB_PORT') ?: '5432';

$dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass";

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error crítico de infraestructura de datos: " . $e->getMessage());
}
?>