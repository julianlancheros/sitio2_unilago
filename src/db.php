<?php
// =========================================================================
// CONFIGURACIÓN DE CONEXIÓN CLOUD POSTGRESQL (RED PRIVADA INTERNA + INTEGRACIÓN SSL)
// =========================================================================

// El servidor lee la variable DATABASE_URL que configuraste en tu panel
$database_url = getenv('DATABASE_URL');

if (!empty($database_url)) {
    // 1. Convertimos el protocolo al formato compatible con el driver de PHP PDO
    $dsn = str_replace('postgresql://', 'pgsql://', $database_url);
    
    // 2. TRUCO DE INFRAESTRUCTURA: Forzar el parámetro sslmode al final de la URL interna
    if (!str_contains($dsn, 'sslmode=')) {
        $dsn .= (str_contains($dsn, '?') ? '&' : '?') . 'sslmode=require';
    }
} else {
    // Plan de respaldo con credenciales directas por si acaso
    $dsn = "pgsql:host=dpg-d8kbnksvikkc73crpg10-a.oregon-postgres.render.com;port=5432;dbname=db_resenas_unilago;user=db_resenas_unilago_user;password=G8s4D3X5DYhrTXM5MHUpQB5M1iYPWzFq;sslmode=require";
}

try {
    // Instanciamos la conexión pasando la DSN con el SSL incrustado
    $pdo = new PDO($dsn);
    
    // Configuramos el manejo de excepciones estructurales
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Freno controlado ante fallos de conexión
    die("Error crítico de infraestructura de datos: " . $e->getMessage());
}
?>
