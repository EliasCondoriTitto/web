<?php
include("../includes/db.php");
include("auth_products.php");

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['id_usuario'];

if (isset($_GET['id_producto'])) {
    $id_producto = $_GET['id_producto'];
    
    if (!canManageProduct($conn, $user_id, $id_producto)) {
        $_SESSION['message'] = 'No tienes permiso para eliminar este producto';
        $_SESSION['message_type'] = 'danger';
        header("Location: index.php");
        exit();
    }
    
    // Iniciar una transacción
    mysqli_begin_transaction($conn);
    
    try {
        // Primero, eliminar la relación en la tabla 'esta'
        $query_esta = "DELETE FROM esta WHERE IdProducto = $id_producto";
        mysqli_query($conn, $query_esta);
        
        // Luego, eliminar el producto
        $query_producto = "DELETE FROM producto WHERE id_producto = $id_producto";
        mysqli_query($conn, $query_producto);
        
        // Si todo salió bien, confirmar la transacción
        mysqli_commit($conn);
        
        $_SESSION['message'] = 'Producto eliminado correctamente';
        $_SESSION['message_type'] = 'success';
    } catch (Exception $e) {
        // Si algo salió mal, revertir la transacción
        mysqli_rollback($conn);
        
        $_SESSION['message'] = 'Error al eliminar el producto: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    }
    
    header("Location: index.php");
}
?>