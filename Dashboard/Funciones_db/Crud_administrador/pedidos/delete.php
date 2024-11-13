<?php
include('../includes/db.php');

if (isset($_GET['id_pedido_carrito'])) {
    $id_pedido_carrito = $_GET['id_pedido_carrito'];
    $query = "DELETE FROM pedido_carrito WHERE id_pedido_carrito = $id_pedido_carrito";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error al eliminar el pedido: " . mysqli_error($conn));
    }

    $_SESSION['message'] = 'Pedido eliminado con éxito';
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php');
}
?>
