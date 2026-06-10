<?php
// =========================================================================
// CONFIGURACIÓN DE CONEXIÓN CLOUD POSTGRESQL (RENDER) - SITIO 2 (UNILAGO)
// =========================================================================

$host = getenv('DB_HOST') ?: 'dpg-d8kbnksvikkc73crpg10-a.oregon-postgres.render.com';
$db   = getenv('DB_NAME') ?: 'db_resenas_unilago';
$user = getenv('DB_USER') ?: 'db_resenas_unilago_user';
$pass = getenv('DB_PASS') ?: 'G8s4D3X5DYhrTXM5MHUpQB5M1iYPWzFq';
$port = getenv('DB_PORT') ?: '5432';

// Construcción del Data Source Name (DSN) con el modo SSL requerido forzado
$dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass;sslmode=require";

try {
    // Inicialización del objeto PDO para la conexión
    $pdo = new PDO($dsn);
    
    // Configuración para que PDO lance excepciones en caso de errores de SQL o infraestructura
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Bloque de contingencia ante fallos de red o credenciales inválidas
    die("Error crítico de infraestructura de datos: " . $e->getMessage());
}
?>
