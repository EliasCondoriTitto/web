<?php
include("../includes/db.php");

if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];

    // Primero, obtenemos el rol del usuario
    $query = "SELECT rol FROM usuario WHERE id_usuario = '$id_usuario'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $rol = $row['rol'];

        // Eliminar registros de las tablas correspondientes según el rol
        if ($rol == 'administrador') {
            $query_admin = "DELETE FROM administrador WHERE id_administrador = '$id_usuario'";
            mysqli_query($conn, $query_admin);
            
        } elseif ($rol == 'comprador') {
            $query_comprador = "DELETE FROM comprador WHERE id_comprador = '$id_usuario'";
            mysqli_query($conn, $query_comprador);
            
        } elseif ($rol == 'vendedor') {
            $query_comunario = "DELETE FROM comunario WHERE id_comunario = '$id_usuario'";
            mysqli_query($conn, $query_comunario);
            
        } elseif ($rol == 'delivery') {
            $query_delivery = "DELETE FROM delivery WHERE id_delivery = '$id_usuario'";
            mysqli_query($conn, $query_delivery);
        }

        // Finalmente, eliminar el usuario de la tabla usuario
        $query_usuario = "DELETE FROM usuario WHERE id_usuario = '$id_usuario'";
        $result_usuario = mysqli_query($conn, $query_usuario);

        if (!$result_usuario) {
            die("Error al eliminar el usuario: " . mysqli_error($conn));
        }

        $_SESSION['message'] = 'Usuario eliminado correctamente';
        $_SESSION['message_type'] = 'danger';
        header("Location: index.php");
    } else {
        $_SESSION['message'] = 'Usuario no encontrado';
        $_SESSION['message_type'] = 'danger';
        header("Location: index.php");
    }
}
?>
