<?php 
include("../includes/db.php");

if(isset($_POST['save'])) {
    $cantidad = $_POST['cantidad'];  
    $estado_pedido = $_POST['estado_pedido'];
    $fecha_pedido = $_POST['fecha_pedido'];
    $id_producto = $_POST['id_producto'];
    $id_pago = $_POST['id_pago'];
    $id_comprador = $_POST['id_comprador'];

    // Verificar si el id_producto existe
    $query_producto = "SELECT * FROM producto WHERE id_producto = '$id_producto'";
    $result_producto = mysqli_query($conn, $query_producto);
    if (mysqli_num_rows($result_producto) == 0) {
        die("Error: El producto con ID $id_producto no existe.");
    }

    // Verificar si el id_pago existe
    $query_pago = "SELECT * FROM pago WHERE id_pago = '$id_pago'";
    $result_pago = mysqli_query($conn, $query_pago);
    if (mysqli_num_rows($result_pago) == 0) {
        die("Error: El pago con ID $id_pago no existe.");
    }

    // Verificar si el id_comprador existe
    $query_comprador = "SELECT * FROM comprador WHERE id_comprador = '$id_comprador'";
    $result_comprador = mysqli_query($conn, $query_comprador);
    if (mysqli_num_rows($result_comprador) == 0) {
        die("Error: El comprador con ID $id_comprador no existe.");
    }

    $query = "INSERT INTO pedido_carrito(cantidad, estado_pedido, fecha_pedido, id_producto, id_pago, id_comprador) 
              VALUES ('$cantidad', '$estado_pedido', '$fecha_pedido', '$id_producto', '$id_pago','$id_comprador')";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error al guardar el pedido: " . mysqli_error($conn));
    }

    $_SESSION['message'] = "Pedido guardado con éxito.";
    $_SESSION['message_type'] = 'success';

    header("Location: index.php");
}
?>
