<?php
session_start();

// Verificar que el usuario es de tipo 'usuario' (solo vista)
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "usuario") {
    header("Location: index.php");
    exit;
}

// Conexión a la base de datos
require_once "conexion.php";

// Obtener lista de aspirantes ordenada
$result = $conexion->query("SELECT * FROM aspirantes ORDER BY id DESC");
$aspirantes = $result->fetch_all(MYSQLI_ASSOC);

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aspirantes - Universidad Gerardo Barrios</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .header { background: #1e3a5f; color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 24px; }
        .btn-logout { display: inline-block; background: #e53e3e; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        .container { max-width: 1400px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .card h2 { color: #1e3a5f; margin-bottom: 20px; border-bottom: 2px solid #2c5282; padding-bottom: 10px; }
        .info-badge { display: inline-block; background: #4299e1; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; margin-left: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #2c5282; color: white; }
        tr:hover { background: #f7fafc; }
        .null-value { color: #a0aec0; font-style: italic; }
        .empty-message { text-align: center; padding: 40px; color: #718096; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Universidad Gerardo Barrios - Vista de Aspirantes</h1>
        <div>
            <span>Usuario: <?php echo htmlspecialchars($_SESSION["nombre_completo"]); ?></span>
            <span class="info-badge">Solo Vista</span>
            <a href="logout.php" class="btn-logout" style="margin-left: 15px;">Cerrar Sesión</a>
        </div>
    </div>
    
    <div class="container">
        <div class="card">
            <h2>Lista de Aspirantes Inscritos <span style="font-size: 14px; color: #718096;">(Solo lectura - No puede realizar modificaciones)</span></h2>
            
            <?php if (empty($aspirantes)): ?>
            <div class="empty-message">
                <p>No hay aspirantes registrados aún.</p>
            </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>DUI</th>
                        <th>Correo Electrónico</th>
                        <th>Teléfono</th>
                        <th>Teléfono Alt.</th>
                        <th>Carrera</th>
                        <th>Turno</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aspirantes as $asp): ?>
                    <tr>
                        <td><?php echo $asp["id"]; ?></td>
                        <td><?php echo htmlspecialchars($asp["nombres"] . " " . $asp["apellidos"]); ?></td>
                        <td><?php echo htmlspecialchars($asp["DUI"]); ?></td>
                        <td><?php echo htmlspecialchars($asp["correo_electronico"]); ?></td>
                        <td><?php echo htmlspecialchars($asp["telefono"]); ?></td>
                        <td><?php echo $asp["telefono_alternativo"] ? htmlspecialchars($asp["telefono_alternativo"]) : '<span class="null-value">NULL</span>'; ?></td>
                        <td><?php echo htmlspecialchars($asp["carrera"]); ?></td>
                        <td><?php echo htmlspecialchars($asp["turno"]); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="margin-top: 20px; color: #718096; font-size: 14px;">
                <strong>Total de aspirantes:</strong> <?php echo count($aspirantes); ?>
            </p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>