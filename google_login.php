<?php
// Inicia sesión si no está iniciada
session_start();

// Incluir la conexión a la base de datos
include('db.php');

// Recibir el token de Google
$data = json_decode(file_get_contents("php://input"));

if (isset($data->id_token)) {
    $id_token = $data->id_token;

    // Verificar el token usando la API de Google
    $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $id_token;
    $response = file_get_contents($url);
    $user_info = json_decode($response);

    if (isset($user_info->email)) {
        // Verifica si el correo electrónico ya existe en la base de datos
        $email = $user_info->email;
        $name = $user_info->name;

        $query = "SELECT * FROM usuarios WHERE correo = '$email' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            // El usuario ya existe, iniciar sesión
            $user = mysqli_fetch_assoc($result);
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol']; // Asumiendo que tienes roles
        } else {
            // Si no existe, crearlo
            $query = "INSERT INTO usuarios (nombre, correo, rol) VALUES ('$name', '$email', 'comprador')";
            mysqli_query($conn, $query);

            // Iniciar sesión después de la creación
            $user_id = mysqli_insert_id($conn);
            $_SESSION['id_usuario'] = $user_id;
            $_SESSION['nombre'] = $name;
            $_SESSION['rol'] = 'comprador'; // O el rol que consideres adecuado
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Token inválido']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Token no recibido']);
}
?>
