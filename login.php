<?php
// Inicia la sesión
session_start();

// Conexión a la base de datos
include('db.php');

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Verifica si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Consulta para verificar el usuario
    $query = "SELECT * FROM usuario WHERE correo = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    // Si se encuentra el usuario
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verificar la contraseña (ajusta esta línea si las contraseñas no están hasheadas)
        if (password_verify($password, $user['contraseña'])) {
        //if ($password == $user['contraseña']) { // Solo para pruebas sin hash

            // Guardar datos de sesión
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol']; // Guardamos el rol del usuario

            // Después de la validación exitosa, usa un script para cerrar el modal
            

            // Redirigir según el rol del usuario
            /*switch ($user['rol']) {
                case 'administrador':
                    header('Location: Dashboard/Dashboard_administrador.php?id_usuario=' . $user['id_usuario']);
                    break;
                case 'vendedor':  // comunario
                    header('Location: Dashboard/Dashboard_comunario.php?id_usuario=' . $user['id_usuario']);
                    break;
                case 'comprador':
                    header('Location: Dashboard/Dashboard_comprador.php?id_usuario=' . $user['id_usuario']);
                    break;
                case 'delivery':
                    header('Location: Dashboard/Dashboard_delivery.php?id_usuario=' . $user['id_usuario']);
                    break;
                default:
                    // Si el rol no está definido, redirigir al login con mensaje de error
                    $error_message = 'Rol no válido. Contacta al administrador.';
                    break;
            }*/
            header('Location: index.php');

            // Enviar respuesta exitosa para cerrar el modal y recargar la página
            echo '<script>
                alert("Inicio de sesión exitoso");
                window.parent.document.getElementById("loginModal").style.display = "none"; // Cierra el modal
                window.parent.location.reload(); // Recarga la página principal
            </script>';

            exit;
        } else {
            $error_message = 'Contraseña incorrecta';
        }
    } else {
        $error_message = 'Usuario no encontrado';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <link rel="stylesheet" href="css/styles.css">
    <!-- Script de Google para Sign-In -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h2>Iniciar Sesión</h2>
        <p>Accede a tu cuenta</p>

        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Iniciar Sesión</button>
        </form>
        <a href="#">¿Olvidaste tu contraseña?</a>

        <!-- Botón de registro -->
        <p>¿No tienes una cuenta? <a href="registro.php">Registrate</a></p>

        <!-- Botón de Google Sign-In -->
        <div id="g_id_onload"
             data-client_id="TU_CLIENTE_ID.apps.googleusercontent.com"
             data-login_uri="http://localhost/login.php"
             data-auto_select="true"
             data-itp_support="true">
        </div>
        <div class="g_id_signin" data-type="standard"></div>

        <footer>&copy; 2024 Plataforma Artesanal</footer>
    </div>
</div>

<script>
    function handleCredentialResponse(response) {
        const data = { id_token: response.credential };

        // Enviar el token a tu servidor
        fetch('google_login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        }).then(response => {
            if (response.ok) {
                window.location.href = 'dashboard.php'; // Redirigir si el login es exitoso
            }
        });
    }
    window.onload = function () {
        google.accounts.id.initialize({
            client_id: 'TU_CLIENTE_ID.apps.googleusercontent.com',
            callback: handleCredentialResponse
        });
        google.accounts.id.prompt(); // Mostrar el prompt de Google
    };

</script>

</body>
</html>
