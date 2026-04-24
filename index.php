<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Universidad Gerardo Barrios</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            color: #1e3a5f;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .logo p {
            color: #666;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #2c5282;
        }
        .form-group input.error {
            border-color: #e53e3e;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #1e3a5f;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background: #2c5282;
        }
        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }
        .error-message.show {
            display: block;
        }
        .info-box {
            background: #ebf8ff;
            border: 1px solid #4299e1;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 13px;
            color: #2c5282;
        }
        .info-box strong {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>Universidad Gerardo Barrios</h1>
            <p>Sistema de Inscripción de Estudiantes</p>
        </div>
        
        <?php
        session_start();
        
        $error = "";
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            require_once "conexion.php";
            
            $username = trim($_POST["username"] ?? "");
            $password = $_POST["password"] ?? "";
            
            // Validaciones del lado del servidor
            if (empty($username)) {
                $error = "El campo usuario es obligatorio.";
            } elseif (empty($password)) {
                $error = "El campo contraseña es obligatorio.";
            } elseif (strlen($password) < 6) {
                $error = "La contraseña debe tener al menos 6 caracteres.";
            } else {
                // Verificar usuario en la base de datos
                $stmt = $conexion->prepare("SELECT id, username, password, tipo_usuario, nombre_completo FROM usuarios WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();
                    
                    if ($password === $user["password"]) {
                        // Login exitoso
                        $_SESSION["user_id"] = $user["id"];
                        $_SESSION["username"] = $user["username"];
                        $_SESSION["tipo_usuario"] = $user["tipo_usuario"];
                        $_SESSION["nombre_completo"] = $user["nombre_completo"];
                        
                        // Redireccionar según tipo de usuario
                        if ($user["tipo_usuario"] == "admin") {
                            header("Location: admin.php");
                            exit;
                        } else {
                            header("Location: vista_publica.php");
                            exit;
                        }
                    } else {
                        $error = "Contraseña incorrecta.";
                    }
                } else {
                    $error = "Usuario no encontrado.";
                }
                $stmt->close();
            }
            $conexion->close();
        }
        ?>
        
        <?php if (!empty($error)): ?>
        <div class="error-message show"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                       required minlength="3" maxlength="50"
                       pattern="[a-zA-Z0-9_]+"
                       title="Solo letras, números y guión bajo">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required minlength="6" maxlength="100">
            </div>
            
            <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>
        
        <div class="info-box">
            <strong>Credenciales de prueba:</strong>
            <p><strong>Admin:</strong> admin / password123</p>
            <p><strong>Usuario:</strong> usuario / password123</p>
        </div>
    </div>
</body>
</html>