<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_producto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];
    $detalles = $_POST['detalles'];

    // Verificar si el carrito ya existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Añadir el producto al carrito
    $_SESSION['carrito'][] = [
        'id_producto' => $id_producto,
        'cantidad' => $cantidad,
        'detalles' => $detalles
    ];

    // Redireccionar de vuelta a la página de productos
    header("Location: productos-artesania-bolivia.php");
    exit;
}
?>
