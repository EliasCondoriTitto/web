


save.php
<?php
include("../includes/db.php");

if (isset($_POST['save'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];
    $fecha_naci = $_POST['fecha_naci'];
    $telefono = $_POST['telefono'];
    $fecha_registro = date("Y-m-d H:i:s");
    $estado = "activo";
    $rol = $_POST['rol'];

     // Encriptar la contraseña
     $hashed_password = password_hash($contraseña, PASSWORD_BCRYPT);

    // Insertar en la tabla USUARIO
    $query = "INSERT INTO usuario(nombre, apellido, correo, contraseña, fecha_naci, telefono, fecha_registro, estado, rol) 
              VALUES ('$nombre', '$apellido', '$correo', '$hashed_password', '$fecha_naci', '$telefono', NOW(), '$estado', '$rol')";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error al insertar en la tabla usuario: " . mysqli_error($conn));
    }

    // Obtener el ID del último usuario insertado
    $id_usuario = mysqli_insert_id($conn);

    // Verificar el rol y guardar en las tablas correspondientes
    if ($rol == 'administrador') {
        // Guardar en la tabla administrador
        $query_admin = "INSERT INTO administrador (id_administrador) VALUES ('$id_usuario')";
        mysqli_query($conn, $query_admin);
        
    } elseif ($rol == 'comprador') {
        // Guardar en la tabla comprador
        $query_comprador = "INSERT INTO comprador (id_comprador) VALUES ('$id_usuario')";
        mysqli_query($conn, $query_comprador);
        
    } elseif ($rol == 'vendedor') {
        // Guardar en la tabla comunario y asociarlo a la comunidad seleccionada
        $id_comunidad = $_POST['id_comunidad'];
        $query_comunario = "INSERT INTO comunario (id_comunario, id_comunidad) VALUES ('$id_usuario', '$id_comunidad')";
        mysqli_query($conn, $query_comunario);
        
    } elseif ($rol == 'delivery') {
        // Guardar en la tabla delivery y asociarlo a la empresa seleccionada
        $id_empresa = $_POST['id_empresa'];
        $query_delivery = "INSERT INTO delivery (id_delivery, id_empresa) VALUES ('$id_usuario', '$id_empresa')";
        mysqli_query($conn, $query_delivery);
    }

    $_SESSION['message'] = 'Usuario guardado correctamente';
    $_SESSION['message_type'] = 'success';
    header("Location: index.php");
}
?>