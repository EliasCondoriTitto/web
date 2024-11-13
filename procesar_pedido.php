<?php
// Iniciar sesión para manejar datos del usuario
session_start();

// Configuración de la base de datos
require_once 'db.php';

// Verificar si se recibió una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

try {
    // Insertar datos en la tabla PAGO
    $sql_pago = "INSERT INTO PAGO (monto, fecha_pago, tipo_pago, estado_pago) VALUES (?, NOW(), ?, 'pendiente')";
    $stmt_pago = $conn->prepare($sql_pago);
    $stmt_pago->bind_param('ds', $total_general, $metodo_pago);
    $stmt_pago->execute();
    $id_pago = $conn->insert_id;

    // Insertar datos en la tabla PEDIDO_CARRITO
    $sql_pedido = "INSERT INTO PEDIDO_CARRITO (cantidad, estado_pedido, fecha_pedido, costo_envio, pdf_factura, id_producto, id_pago, id_comprador, id_ubicacion)
                   VALUES (?, 'pendiente', NOW(), ?, NULL, ?, ?, ?, ?)";
    $stmt_pedido = $conn->prepare($sql_pedido);
    $stmt_pedido->bind_param('ddiii', $cantidad, $costo_envio, $producto_id, $id_pago, $id_comprador, $ubicacion_id);
    $stmt_pedido->execute();
    $id_pedido = $conn->insert_id;

    // Insertar datos en la tabla ENTREGA_DELIVERY
    $sql_entrega = "INSERT INTO ENTREGA_DELIVERY (datos_entrega, fecha_entrega, estado_entrega, id_pedido_carrrito, id_delivery)
                    VALUES (?, NOW(), 'pendiente', ?, ?)";
    $stmt_entrega = $conn->prepare($sql_entrega);
    $datos_entrega = json_encode([
        'direccion' => $direccion_entrega,
        'referencia' => $referencia,
        'latitud' => $latitud,
        'longitud' => $longitud
    ]);
    $stmt_entrega->bind_param('sii', $datos_entrega, $id_pedido, $empresa_delivery);
    $stmt_entrega->execute();

    // Confirmar la transacción
    $conn->commit();

    // Redirigir a generar_pdf.php para crear el PDF del pedido
    header("Location: generar_pdf.php?id_pedido=" . $id_pedido);
    exit();

} catch (Exception $e) {
    // En caso de error, deshacer la transacción
    $conn->rollback();
    echo "Error al confirmar el pedido: " . $e->getMessage();
}