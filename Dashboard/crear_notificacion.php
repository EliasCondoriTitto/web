<?php
require_once 'db.php';

function crearNotificacion($id_usuario, $mensaje, $id_pedido) {
    global $conn;
    $sql = "INSERT INTO NOTIFICACION (id_usuario, mensaje, estado, fecha_creacion, id_pedido) VALUES (?, ?, 'enviado', NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $id_usuario, $mensaje, $id_pedido);
    $stmt->execute();
    $stmt->close();
}
?>
