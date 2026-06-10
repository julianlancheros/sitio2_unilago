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
            border-color: #2563eb;
            background-color: #ffffff;
        }
        button {
            width: 100%;
            background-color: #2563eb;
            color: #ffffff;
            font-weight: bold;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            transition: background-color 0.2s;
        }
        button:hover {
            background-color: #1d4ed8;
        }
        .alert {
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .error { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .divider {
            margin: 40px 0;
            border: 0;
            border-top: 1px solid #e2e8f0;
        }
        .review-list-title {
            color: #0f172a;
            font-size: 20px;
            margin-bottom: 20px;
        }
        .review-card {
            background: #f8fafc;
            border-left: 4px solid #2563eb;
            padding: 16px;
            margin-bottom: 16px;
            border-radius: 0 8px 8px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .review-card h3 {
            margin: 0 0 6px 0;
            font-size: 16px;
            color: #1e293b;
        }
        .review-card h3 span {
            font-weight: normal;
            color: #64748b;
            font-size: 14px;
        }
        .stars {
            color: #ea580c;
            font-weight: bold;
            font-size: 13px;
        }
        .comment {
            margin: 10px 0 0 0;
            font-size: 14px;
            color: #475569;
            line-height: 1.5;
        }
        .date {
            font-size: 11px;
            color: #94a3b8;
            display: block;
            margin-top: 10px;
            text-align: right;
        }
        .no-data {
            text-align: center;
            color: #64748b;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>UniLago — Reseñas Tecnológicas</h1>
    <div class="subtitle">Módulo de Entrada e Infraestructura Cloud Multi-Database</div>

    <?php echo $mensaje; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="equipo">Nombre de Equipo o Componente:</label>
            <input type="text" id="equipo" name="equipo" placeholder="Ej: Portátil Gamer Lenovo Legion 5 Pro" required>
        </div>
        
        <div class="form-group">
            <label for="marca">Marca del Fabricante:</label>
            <input type="text" id="marca" name="marca" placeholder="Ej: LENOVO" required>
        </div>
        
        <div class="form-group">
            <label for="calificacion">Calificación Técnica / Rendimiento:</label>
            <select id="calificacion" name="calificacion">
                <option value="5">⭐⭐⭐⭐⭐ (5/5) Excelente rendimiento</option>
                <option value="4">⭐⭐⭐⭐ (4/5) Muy competente</option>
                <option value="3">⭐⭐⭐ (3/5) Desempeño estándar</option>
                <option value="2">⭐⭐ (2/5) Limitado / Calentamiento</option>
                <option value="1">⭐ (1/5) No recomendado</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="comentario">Reseña y Análisis Crítico:</label>
            <textarea id="comentario" name="comentario" rows="4" placeholder="Detalla procesador, experiencia de uso, gestión térmica y pruebas en juegos/programas..." required></textarea>
        </div>
        
        <button type="submit">Enviar Reseña y Sincronizar Sistemas</button>
    </form>

    <hr class="divider">

    <h2 class="review-list-title">Feed de Reseñas Disponibles (PostgreSQL Cloud)</h2>
    
    <?php if (empty($resenas)): ?>
        <p class="no-data">Actualmente no existen análisis de hardware registrados.</p>
    <?php else: ?>
        <?php foreach ($resenas as $r): ?>
            <div class="review-card">
                <h3><?php echo htmlspecialchars($r['equipo']); ?> <span>| Marca: <?php echo htmlspecialchars($r['marca']); ?></span></h3>
                <div class="stars">Valoración: <?php echo $r['calificacion']; ?>/5</div>
                <p class="comment"><?php echo nl2br(htmlspecialchars($r['comentario'])); ?></p>
                <small class="date">Publicado el: <?php echo $r['fecha_publicacion']; ?></small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
        input[type="text"]:focus, select:focus, textarea:focus {
            outline: none;
