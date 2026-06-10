<?php
// =========================================================================
// CONFIGURACIÓN DE CONEXIÓN REAL - POSTGRESQL (RENDER) CON INTEGRACIÓN SSL
// =========================================================================

// Evaluamos si existen variables en el panel de Render, de lo contrario usamos tus credenciales fijas
$host = getenv('DB_HOST') ?: 'dpg-d8kbnksvikkc73crpg10-a.oregon-postgres.render.com';
$db   = getenv('DB_NAME') ?: 'db_resenas_unilago';
$user = getenv('DB_USER') ?: 'db_resenas_unilago_user';
$pass = getenv('DB_PASS') ?: 'G8s4D3X5DYhrTXM5MHUpQB5M1iYPWzFq';
$port = getenv('DB_PORT') ?: '5432';

// Construcción limpia del DSN forzando el sslmode requerido por Render
$dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass;sslmode=require";

try {
    // Instanciamos PDO pasando los parámetros directamente
    $pdo = new PDO($dsn);
    
    // Configuramos el manejo de errores para que lance excepciones estructuradas
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // En caso de fallar, el servidor frena la ejecución de forma controlada
    die("Error crítico de infraestructura de datos: " . $e->getMessage());
}
?>
