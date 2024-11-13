<?php
include("../includes/db.php");

if (isset($_GET['id_almacen'])) {
    $id_almacen = $_GET['id_almacen'];
    
    // Iniciar transacción
    mysqli_begin_transaction($conn);
    
    try {
        // Primero eliminar la ubicación asociada
        $query_ubicacion = "DELETE FROM ubicacion WHERE id_almacen = ?";
        $stmt_ubicacion = mysqli_prepare($conn, $query_ubicacion);
        mysqli_stmt_bind_param($stmt_ubicacion, "i", $id_almacen);
        mysqli_stmt_execute($stmt_ubicacion);

        // Luego eliminar el almacén
        $query_almacen = "DELETE FROM almacen WHERE id_almacen = ?";
        $stmt_almacen = mysqli_prepare($conn, $query_almacen);
        mysqli_stmt_bind_param($stmt_almacen, "i", $id_almacen);
        mysqli_stmt_execute($stmt_almacen);

        // Confirmar transacción
        mysqli_commit($conn);

        $_SESSION['message'] = 'Almacén y ubicación eliminados exitosamente.';
        $_SESSION['message_type'] = 'danger';
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        mysqli_rollback($conn);
        $_SESSION['message'] = 'Error al eliminar el almacén: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    }

    header('Location: index.php');
}
?>