<?php
// =========================================================================
// CONFIGURACIÓN DE CONEXIÓN REAL - POSTGRESQL (RED INTERNA DE RENDER)
// =========================================================================

// Usamos el host interno para viajar por la red privada de Render sin bloqueos de firewall
$host = 'dpg-d8kbnksvikkc73crpg10-a'; 
$db   = 'db_resenas_unilago';
$user = 'db_resenas_unilago_user';
$pass = 'G8s4D3X5DYhrTXM5MHUpQB5M1iYPWzFq';
$port = '5432';

// Construcción del DSN usando el Host Interno privado
$dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass";

try {
    // Instanciamos PDO directamente sobre la intranet de Render
    $pdo = new PDO($dsn);
    
    // Configuramos el manejo de errores para lanzar excepciones estructuradas
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Freno controlado en caso de fallos de infraestructura
    die("Error crítico de infraestructura de datos: " . $e->getMessage());
}
?>
