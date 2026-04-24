<?php
session_start();

// Verificar que el usuario es admin
if (!isset($_SESSION["tipo_usuario"]) || $_SESSION["tipo_usuario"] != "admin") {
    header("Location: index.php");
    exit;
}

$message = "";
$messageType = "";

// Conexión a la base de datos
require_once "conexion.php";

// Procesar formulario de agregar/editar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    
    $action = $_POST["action"];
    
    if ($action == "add" || $action == "edit") {
        // Validar campos requeridos
        $nombres = trim($_POST["nombres"] ?? "");
        $apellidos = trim($_POST["apellidos"] ?? "");
        $dui = trim($_POST["dui"] ?? "");
        $correo = trim($_POST["correo"] ?? "");
        $telefono = trim($_POST["telefono"] ?? "");
        $telefono_alt = trim($_POST["telefono_alternativo"] ?? "");
        $carrera = $_POST["carrera"] ?? "";
        $turno = $_POST["turno"] ?? "";
        
        // Validaciones
        $errors = [];
        
        if (empty($nombres) || strlen($nombres) < 2) {
            $errors[] = "El nombre debe tener al menos 2 caracteres.";
        }
        if (empty($apellidos) || strlen($apellidos) < 2) {
            $errors[] = "Los apellidos deben tener al menos 2 caracteres.";
        }
        if (empty($dui) || !preg_match('/^\d{8}-\d$/', $dui)) {
            $errors[] = "El DUI debe tener el formato 00000000-0.";
        }
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Debe ingresar un correo electrónico válido.";
        }
        if (empty($telefono) || !preg_match('/^\d{4}-\d{4}$/', $telefono)) {
            $errors[] = "El teléfono debe tener el formato 0000-0000.";
        }
        if (empty($carrera)) {
            $errors[] = "Debe seleccionar una carrera.";
        }
        if (empty($turno)) {
            $errors[] = "Debe seleccionar un turno.";
        }
        
        if (empty($errors)) {
            // El teléfono alternativo puede ser null
            $telefono_alt = empty($telefono_alt) ? null : $telefono_alt;
            
            if ($action == "add") {
                // Verificar DUI único
                $stmt = $conexion->prepare("SELECT id FROM aspirantes WHERE DUI = ?");
                $stmt->bind_param("s", $dui);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    $errors[] = "Ya existe un aspirante con ese DUI.";
                } else {
                    $stmt = $conexion->prepare("INSERT INTO aspirantes (nombres, apellidos, DUI, correo_electronico, telefono, telefono_alternativo, carrera, turno) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssss", $nombres, $apellidos, $dui, $correo, $telefono, $telefono_alt, $carrera, $turno);
                    
                    if ($stmt->execute()) {
                        $message = "Aspirante registrado exitosamente.";
                        $messageType = "success";
                    } else {
                        $errors[] = "Error al registrar el aspirante.";
                    }
                }
                $stmt->close();
            } elseif ($action == "edit" && isset($_POST["id"])) {
                $id = intval($_POST["id"]);
                
                // Verificar DUI único (excluyendo el actual)
                $stmt = $conexion->prepare("SELECT id FROM aspirantes WHERE DUI = ? AND id != ?");
                $stmt->bind_param("si", $dui, $id);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    $errors[] = "Ya existe otro aspirante con ese DUI.";
                } else {
                    $stmt = $conexion->prepare("UPDATE aspirantes SET nombres=?, apellidos=?, DUI=?, correo_electronico=?, telefono=?, telefono_alternativo=?, carrera=?, turno=? WHERE id=?");
                    $stmt->bind_param("ssssssssi", $nombres, $apellidos, $dui, $correo, $telefono, $telefono_alt, $carrera, $turno, $id);
                    
                    if ($stmt->execute()) {
                        $message = "Aspirante actualizado exitosamente.";
                        $messageType = "success";
                    } else {
                        $errors[] = "Error al actualizar el aspirante.";
                    }
                }
                $stmt->close();
            }
        }
        
        if (!empty($errors)) {
            $message = implode(" ", $errors);
            $messageType = "error";
        }
    }
}

// Eliminar aspirante
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    $stmt = $conexion->prepare("DELETE FROM aspirantes WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Aspirante eliminado exitosamente.";
        $messageType = "success";
    }
    $stmt->close();
}

// Obtener lista de aspirantes
$result = $conexion->query("SELECT * FROM aspirantes ORDER BY id DESC");
$aspirantes = $result->fetch_all(MYSQLI_ASSOC);

// Si hay edición, obtener datos del aspirante
$editando = null;
if (isset($_GET["edit"])) {
    $edit_id = intval($_GET["edit"]);
    $stmt = $conexion->prepare("SELECT * FROM aspirantes WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result_edit = $stmt->get_result();
    if ($result_edit->num_rows > 0) {
        $editando = $result_edit->fetch_assoc();
    }
    $stmt->close();
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Universidad Gerardo Barrios</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .header { background: #1e3a5f; color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 24px; }
        .btn-logout { background: #e53e3e; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .message { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .message.success { background: #c6f6d5; color: #22543d; border: 1px solid #48bb78; }
        .message.error { background: #fed7d7; color: #c53030; border: 1px solid #f56565; }
        .card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .card h2 { color: #1e3a5f; margin-bottom: 20px; border-bottom: 2px solid #2c5282; padding-bottom: 10px; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #333; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 5px; font-size: 14px; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #2c5282; }
        .btn { display: inline-block; padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; font-size: 13px; font-weight: 600; text-decoration: none; }
        .btn-primary { background: #2c5282; color: white; }
        .btn-primary:hover { background: #1e3a5f; }
        .btn-edit { background: #ed8936; color: white; margin-right: 5px; }
        .btn-delete { background: #e53e3e; color: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #1e3a5f; color: white; }
        tr:hover { background: #f7fafc; }
        .null-value { color: #a0aec0; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Universidad Gerardo Barrios - Panel Admin</h1>
        <div>
            <span>Bienvenido, <?php echo htmlspecialchars($_SESSION["nombre_completo"]); ?></span>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </div>
    
    <div class="container">
        <?php if (!empty($message)): ?>
        <div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <!-- Formulario de inscripción -->
        <div class="card">
            <h2><?php echo $editando ? "Editar Aspirante" : "Nuevo Aspirante"; ?></h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="<?php echo $editando ? "edit" : "add"; ?>">
                <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?php echo $editando["id"]; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombres">Nombres *</label>
                        <input type="text" id="nombres" name="nombres" required minlength="2" maxlength="100" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo letras" value="<?php echo $editando ? htmlspecialchars($editando["nombres"]) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="apellidos">Apellidos *</label>
                        <input type="text" id="apellidos" name="apellidos" required minlength="2" maxlength="100" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" title="Solo letras" value="<?php echo $editando ? htmlspecialchars($editando["apellidos"]) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="dui">DUI *</label>
                        <input type="text" id="dui" name="dui" required pattern="\d{8}-\d" placeholder="00000000-0" maxlength="10" value="<?php echo $editando ? htmlspecialchars($editando["DUI"]) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo Electrónico *</label>
                        <input type="email" id="correo" name="correo" required maxlength="100" value="<?php echo $editando ? htmlspecialchars($editando["correo_electronico"]) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono *</label>
                        <input type="text" id="telefono" name="telefono" required pattern="\d{4}-\d{4}" placeholder="0000-0000" maxlength="9" value="<?php echo $editando ? htmlspecialchars($editando["telefono"]) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="telefono_alternativo">Teléfono Alternativo</label>
                        <input type="text" id="telefono_alternativo" name="telefono_alternativo" pattern="\d{4}-\d{4}" placeholder="0000-0000 (opcional)" maxlength="9" value="<?php echo $editando ? htmlspecialchars($editando["telefono_alternativo"] ?? '') : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="carrera">Carrera *</label>
                        <select id="carrera" name="carrera" required>
                            <option value="">Seleccionar carrera</option>
                            <option value="Ingeniería en Sistemas" <?php echo ($editando && $editando["carrera"] == "Ingeniería en Sistemas") ? 'selected' : ''; ?>>Ingeniería en Sistemas</option>
                            <option value="Ingeniería Civil" <?php echo ($editando && $editando["carrera"] == "Ingeniería Civil") ? 'selected' : ''; ?>>Ingeniería Civil</option>
                            <option value="Ingeniería Industrial" <?php echo ($editando && $editando["carrera"] == "Ingeniería Industrial") ? 'selected' : ''; ?>>Ingeniería Industrial</option>
                            <option value="Licenciatura en Administración" <?php echo ($editando && $editando["carrera"] == "Licenciatura en Administración") ? 'selected' : ''; ?>>Licenciatura en Administración</option>
                            <option value="Licenciatura en Derecho" <?php echo ($editando && $editando["carrera"] == "Licenciatura en Derecho") ? 'selected' : ''; ?>>Licenciatura en Derecho</option>
                            <option value="Licenciatura en Contaduría" <?php echo ($editando && $editando["carrera"] == "Licenciatura en Contaduría") ? 'selected' : ''; ?>>Licenciatura en Contaduría</option>
                            <option value="Licenciatura en Psicología" <?php echo ($editando && $editando["carrera"] == "Licenciatura en Psicología") ? 'selected' : ''; ?>>Licenciatura en Psicología</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="turno">Turno *</label>
                        <select id="turno" name="turno" required>
                            <option value="">Seleccionar turno</option>
                            <option value="Matutino" <?php echo ($editando && $editando["turno"] == "Matutino") ? 'selected' : ''; ?>>Matutino</option>
                            <option value="Vespertino" <?php echo ($editando && $editando["turno"] == "Vespertino") ? 'selected' : ''; ?>>Vespertino</option>
                            <option value="Fin de Semana" <?php echo ($editando && $editando["turno"] == "Fin de Semana") ? 'selected' : ''; ?>>Fin de Semana</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo $editando ? "Actualizar" : "Registrar"; ?> Aspirante</button>
                <?php if ($editando): ?>
                <a href="admin.php" class="btn" style="margin-left: 10px; background: #718096; color: white; text-decoration: none;">Cancelar</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Lista de aspirantes -->
        <div class="card">
            <h2>Aspirantes Inscritos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Completo</th>
                        <th>DUI</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Teléfono Alt.</th>
                        <th>Carrera</th>
                        <th>Turno</th>
                        <th>Acciones</th>
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
                        <td>
                            <a href="admin.php?edit=<?php echo $asp["id"]; ?>" class="btn btn-edit">Editar</a>
                            <a href="admin.php?delete=<?php echo $asp["id"]; ?>" class="btn btn-delete" onclick="return confirm('¿Está seguro de eliminar este aspirante?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>