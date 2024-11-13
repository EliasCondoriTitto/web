<?php
include("../includes/db.php");

if (isset($_POST['save'])) {
    $nombre = $_POST['nombre'];
    $departamento = $_POST['departamento'];
    $provincia = $_POST['provincia'];
    $calle = $_POST['calle'];
    $zona = $_POST['zona'];
    $nro_puerta = $_POST['nro_puerta'];
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];
    $fecha_registro = date('Y-m-d H:i:s');

    // Iniciar transacción
    mysqli_begin_transaction($conn);

    try {
        // Insertar en la tabla almacen
        $query_almacen = "INSERT INTO almacen(nombre, fecha_registro) VALUES (?, ?)";
        $stmt_almacen = mysqli_prepare($conn, $query_almacen);
        mysqli_stmt_bind_param($stmt_almacen, "ss", $nombre, $fecha_registro);
        
        if (!mysqli_stmt_execute($stmt_almacen)) {
            throw new Exception("Error al insertar el almacén: " . mysqli_stmt_error($stmt_almacen));
        }
        
        $id_almacen = mysqli_insert_id($conn);

        // Insertar en la tabla ubicacion
        $query_ubicacion = "INSERT INTO ubicacion(departamento, provincia, calle, zona, nro_puerta, latitud, longitud, id_almacen) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_ubicacion = mysqli_prepare($conn, $query_ubicacion);
        mysqli_stmt_bind_param($stmt_ubicacion, "sssssddi", 
                              $departamento, $provincia, $calle, $zona, 
                              $nro_puerta, $latitud, $longitud, $id_almacen);
        
        if (!mysqli_stmt_execute($stmt_ubicacion)) {
            throw new Exception("Error al insertar la ubicación: " . mysqli_stmt_error($stmt_ubicacion));
        }

        mysqli_commit($conn);
        $_SESSION['message'] = 'Almacén guardado exitosamente.';
        $_SESSION['message_type'] = 'success';
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['message'] = 'Error al guardar el almacén: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    } finally {
        if (isset($stmt_almacen)) mysqli_stmt_close($stmt_almacen);
        if (isset($stmt_ubicacion)) mysqli_stmt_close($stmt_ubicacion);
    }

    header("Location: index.php");
}
?>