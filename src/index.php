<?php
// ==========================================
// 1. IMPORTACIÓN DE LA CONEXIÓN A POSTGRESQL
// ==========================================
require_once __DIR__ . '/db.php'; // Carga el archivo db.php con el objeto $pdo

$mensaje = ""; // Variable para almacenar el estado de las operaciones

// ==========================================
// 2. PROCESAMIENTO DEL FORMULARIO (POST)
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitización de entradas para evitar espacios vacíos o scripts maliciosos
    $equipo       = isset($_POST['equipo']) ? trim($_POST['equipo']) : '';
    $marca        = isset($_POST['marca']) ? trim($_POST['marca']) : '';
    $calificacion = isset($_POST['calificacion']) ? intval($_POST['calificacion']) : 5;
    $comentario   = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';

    // Validación estricta en el servidor (Sintaxis Limpia)
    if (!empty($equipo) && !empty($marca) && !empty($comentario)) {
        try {
            // ---------------------------------------------------------
            // PUNTO 4 & 6: Inserción Segura en PostgreSQL (Prepared Statements)
            // ---------------------------------------------------------
            $sql_pg = "INSERT INTO resenas (equipo, marca, calificacion, comentario) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql_pg);
            $stmt->execute([$equipo, $marca, $calificacion, $comentario]);

            // ---------------------------------------------------------
            // PUNTO 8: Respaldo de Datos en MongoDB Atlas (Data API)
            // ---------------------------------------------------------
            // Remplaza con las credenciales que te genere tu clúster de Mongo Atlas
            $mongo_api_key = "TU_MONGO_ATLAS_DATA_API_KEY"; 
            $mongo_url     = "https://data.mongodb-api.com/app/data-xxxx/endpoint/data/v1/action/insertOne";
            
            $mongo_payload = [
                "collection" => "respaldos_resenas",
                "database"   => "unilago_db",
                "datasource" => "Cluster0",
                "document"   => [
                    "equipo"         => $equipo,
                    "marca"          => $marca,
                    "calificacion"   => $calificacion,
                    "comentario"     => $comentario,
                    "fecha_respaldo" => date("Y-m-d H:i:s")
                ]
            ];

            // Configuración de la petición HTTP cURL para la API de MongoDB
            $ch = curl_init($mongo_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'api-key: ' . $mongo_api_key
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($mongo_payload));
            
            // Ejecutar el respaldo en segundo plano
            curl_exec($ch);
            curl_close($ch);

            // Mensaje de éxito si ambas operaciones se ejecutan correctamente
            $mensaje = "<div class='alert success'>✔ Reseña registrada con éxito en PostgreSQL y respaldada en MongoDB Atlas.</div>";

        } catch (Exception $e) {
            // Manejo de excepciones de infraestructura
            $mensaje = "<div class='alert error'>❌ Error al procesar el registro: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $mensaje = "<div class='alert error'>⚠ Todos los campos del formulario son obligatorios.</div>";
    }
}

// ==========================================
// 3. CONSULTA EN TIEMPO REAL (PUNTO 7)
// ==========================================
try {
    $stmt_consulta = $pdo->query("SELECT * FROM resenas ORDER BY fecha_publicacion DESC");
    $resenas = $stmt_consulta->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al consultar las reseñas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniLago - Reseñas Tecnológicas</title>
    <style>
        /* Estilos limpios y corporativos para la sustentación visual */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #0f172a;
            font-size: 24px;
            text-align: center;
            margin-bottom: 5px;
        }
        .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #334155;
            font-size: 14px;
        }
        input[type="text"], select, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background-color: #f8fafc;
            color: #1e293b;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        input[type="text"]:focus, select:focus, textarea:focus {
            outline: none;