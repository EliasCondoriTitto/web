<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "bdproduc_artesanales";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


require_once 'crear_notificacion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pedido = $_POST['id_pedido'];
    $accion = $_POST['accion'];

    if ($accion == 'aceptar') {
        // Cambiar el estado del pedido a "asignado" para indicar que ha sido aceptado
        $sql = "UPDATE PEDIDO_CARRITO SET estado_pedido = 'asignado' WHERE id_pedido_carrito = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_pedido_carrito);
        $stmt->execute();
        $stmt->close();

        // Obtener el ID del comprador y enviar la notificación
        $id_comprador = obtenerIdCompradorPorPedido($id_pedido_carrito);
        crearNotificacion($id_comprador, "Su pedido ha sido aceptado por el delivery.", $id_pedido);
        
    } elseif ($accion == 'enviar_notificacion') {
        $estado_pedido = $_POST['estado_pedido'];
        $id_comprador = obtenerIdCompradorPorPedido($id_pedido_carrito);
        notificarEstadoPedidoComprador($id_comprador, $id_pedido, $estado_pedido);
    }

    header("Location: dashboard_delivery.php");
}

function obtenerIdCompradorPorPedido($id_pedido_carrito) {
    global $conn;
    $sql = "SELECT id_comprador FROM PEDIDO_CARRITO WHERE id_pedido_carrito = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pedido_carrito);
    $stmt->execute();
    $stmt->bind_result($id_comprador);
    $stmt->fetch();
    $stmt->close();
    return $id_comprador;
}

function notificarEstadoPedidoComprador($id_comprador, $id_pedido, $estado_pedido) {
    switch ($estado_pedido) {
        case 'recogido_almacen':
            $mensaje = "Su pedido ha sido recogido del almacén.";
            break;
        case 'en_camino':
            $mensaje = "Su pedido está en camino.";
            break;
        case 'llegando':
            $mensaje = "Su pedido está próximo a llegar.";
            break;
        case 'entregado':
            $mensaje = "Su pedido ha sido entregado.";
            break;
        default:
            return;
    }
    crearNotificacion($id_comprador, $mensaje, $id_pedido);
}
?>
