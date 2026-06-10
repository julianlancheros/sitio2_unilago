<?php
$host = 'dpg-d8kbnksvikkc73crpg10-a.oregon-postgres.render.com';
$db   = 'db_resenas_unilago';
$user = 'db_resenas_unilago_user';
$pass = 'G8s4D3X5DYhrTXM5MHUpQB5M1iYPWzFq';
$port = '5432';

// Intentamos pasar el parámetro directamente en la cadena
$dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass;sslmode=require";

try {
    // Le pasamos opciones adicionales a PDO para forzar la verificación y el SSL
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Algunas configuraciones de PHP nativo requieren este comando para entornos Postgres Cloud
        PDO:: stillness_placeholder => isset($options) 
    ];
    
    $pdo = new PDO($dsn, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
} catch (PDOException $e) {
    die("Error crítico de infraestructura de datos: " . $e->getMessage());
}
?>
