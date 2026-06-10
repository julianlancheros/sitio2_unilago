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


// =========================================================================
// NUEVA FUNCIÓN: LECTURA DESDE MONGODB ATLAS (Estructura NoSQL)
// =========================================================================
function obtenerResenasMongoDB() {
    // Esta es la URL con tus credenciales y el nombre de tu clúster "Taller4Servidores"
    $uri = "mongodb+srv://julianlancheros131_db_user:<db_password>@taller4servidores.oybiloa.mongodb.net/?appName=Taller4Servidores";
    
    if (class_exists('MongoDB\Driver\Manager')) {
        try {
            $manager = new MongoDB\Driver\Manager($uri);
            // Consultamos la colección 'resenas' dentro de tu base de datos NoSQL
            $query = new MongoDB\Driver\Query([], ['sort' => ['fecha_publicacion' => -1]]);
            $cursor = $manager->executeQuery('db_resenas_unilago.resenas', $query);
            
            return iterator_to_array($cursor);
        } catch (Exception $e) {
            return []; // Si falla la red NoSQL, retorna vacío para no romper la web
        }
    }
    
    // Si el contenedor de Render no tiene el driver activo, muestra el estado de respaldo
    return [];
}
?>
