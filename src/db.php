<?php
// =========================================================================
// CONFIGURACIÓN DE CONEXIÓN CLOUD POSTGRESQL (EXTRACCIÓN LIMPIA DE URL)
// =========================================================================

// Leemos la variable exacta que tienes configurada en la captura de pantalla
$database_url = getenv('DATABASE_URL');

if (!empty($database_url)) {
    // Desarmamos la URL de Render de forma segura utilizando la función nativa de PHP
    $dbparts = parse_url($database_url);

    // Mapeamos los componentes individuales para estructurar el DSN nativo de PDO
    $host = $dbparts['host'];
    $port = $dbparts['port'] ?? '5432';
    $user = $dbparts['user'];
    $pass = $dbparts['pass'];
    $db   = ltrim($dbparts['path'], '/');

    // Construimos la cadena DSN limpia eliminando los esquemas con slashes '//'
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass";
} else {
    // Plan de respaldo manual tradicional por si la variable fallara
    $dsn = "pgsql:host=dpg-d8kbnksvikkc73crpg10-a.oregon-postgres.render.com;port=5432;dbname=db_resenas_unilago;user=db_resenas_unilago_user;password=G8s4D3X5DYhrTXM5MHUpQB5M1iYPWzFq;sslmode=require";
}

try {
    // Instanciamos el puente de conexión PDO
    $pdo = new PDO($dsn);
    
    // Forzamos el manejo de excepciones estructurales ante cualquier fallo
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Freno controlado si la base de datos rechaza la petición
    die("Error crítico de infraestructura de datos: " . $e->getMessage());
}
?>
