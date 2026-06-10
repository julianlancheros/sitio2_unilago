<?php
// =========================================================================
// CONFIGURACIÓN DE CONEXIÓN CLOUD POSTGRESQL (MÉTODO DE RED PRIVADA INTERNA)
// =========================================================================

// El servidor web lee la variable DATABASE_URL que configuraste en el paso anterior
$database_url = getenv('DATABASE_URL');

if (!empty($database_url)) {
    // Render entrega el esquema como "postgresql://". 
    // PHP PDO requiere estrictamente "pgsql://" para poder activar su driver nativo.
    $dsn = str_replace('postgresql://', 'pgsql://', $database_url);
} else {
    // Plan de respaldo con credenciales externas por si acaso
    $host = 'dpg-d8kbnksvikkc73crpg10-a.oregon-postgres.render.com';
    $db   = 'db_resenas_unilago';
    $user = 'db_resenas_unilago_user';
    $pass = 'G8s4D3X5DYhrTXM5MHUpQB5M1iYPWzFq';
    $port = '5432';
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass;sslmode=require";
}

try {
    // Instanciamos la conexión pasando la URL ya formateada para PHP
    $pdo = new PDO($dsn);
    
    // Forzamos el manejo de excepciones estructuradas en caso de fallos en los queries
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Freno controlado si la red interna llega a fallar
    die("Error crítico de infraestructura de datos: " . $e->getMessage());
}
?>
