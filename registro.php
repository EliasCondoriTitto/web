<?php
// Iniciar sesión
session_start();

// Incluir el archivo de conexión a la base de datos
include('db.php');

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibir los datos del formulario y proteger contra inyecciones SQL
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Validar formato de correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Por favor, introduce un correo electrónico válido.";
    } elseif ($password !== $password_confirm) {
        $error_message = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 8) {
        $error_message = "La contraseña debe tener al menos 8 caracteres.";
    } else {
        // Verificar si el correo ya está registrado
        $stmt = $conn->prepare("SELECT * FROM usuario WHERE correo = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Este correo electrónico ya está registrado.";
        } else {
            // Encriptar la contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insertar el nuevo usuario en la base de datos
            $stmt = $conn->prepare("INSERT INTO usuario (nombre, correo, contraseña, rol) VALUES (?, ?, ?, 'comprador')");
            $stmt->bind_param("sss", $nombre, $email, $hashed_password);
            if ($stmt->execute()) {
                // Obtener el id del usuario recién insertado
                $id_usuario = $stmt->insert_id;

                // Insertar en la tabla comprador usando el mismo id que el usuario
                $stmt_comprador = $conn->prepare("INSERT INTO comprador (id_comprador) VALUES (?)");
                $stmt_comprador->bind_param("i", $id_usuario);

                if ($stmt_comprador->execute()) {
                    // Si el registro es exitoso, redirigir al login
                    $_SESSION['mensaje'] = "Registro exitoso. Por favor, inicia sesión.";
                    
                    echo "Registro exitoso. Ahora puedes iniciar sesión";
                    header('Location: index.php');
                    exit;
                } else {
                    $error_message = "Error al registrar el comprador. Intenta nuevamente.";
                }

                $stmt_comprador->close();
            } else {
                $error_message = "Error al registrar el usuario. Intenta nuevamente.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h2>Registro de Usuario</h2>

        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form action="registro.php" method="POST">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required>
            
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
            
            <label for="password_confirm">Confirmar Contraseña</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
            
            <button type="submit">Registrarse</button>
        </form>

        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
        <footer>&copy; 2024 Plataforma Artesanal</footer>
    </div>
</div>

</body>
</html>