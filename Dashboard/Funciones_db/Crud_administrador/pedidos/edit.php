<?php
include("../includes/db.php");

$cantidad = '';
$estado_pedido = '';
$fecha_pedido = '';
$id_producto = '';
$id_pago = '';
$id_comprador = '';

if (isset($_GET['id_pedido_carrito'])) {
    $id_pedido_carrito = $_GET['id_pedido_carrito'];
    $query = "SELECT * FROM pedido_carrito WHERE id_pedido_carrito = $id_pedido_carrito";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $cantidad = $row['cantidad'];
        $estado_pedido = $row['estado_pedido'];
        $id_producto = $row['id_producto'];
        $id_pago = $row['id_pago'];
        $id_comprador = $row['id_comprador'];
    }
}

if (isset($_POST['actualizar'])) {
    $id_pedido_carrito = $_GET['id_pedido_carrito'];
    $cantidad = $_POST['cantidad'];
    $estado_pedido = $_POST['estado_pedido'];
    $id_producto = $_POST['id_producto'];
    $id_pago = $_POST['id_pago'];
    $id_comprador = $_POST['id_comprador'];
    $query = "UPDATE pedido_carrito SET cantidad = '$cantidad', estado_pedido = '$estado_pedido', fecha_pedido = '$fecha_pedido', id_producto = '$id_producto', id_pago = '$id_pago', id_comprador = '$id_comprador' WHERE id_pedido_carrito = $id_pedido_carrito";
    mysqli_query($conn, $query);

    $_SESSION['message'] = 'Pedido actualizado con éxito';
    $_SESSION['message_type'] = 'warning';
    
    header('Location: index.php');
}
?>

<?php include('../includes/header.php'); ?>
<div class="container p-4">
    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card card-body">
                <form action="edit.php?id_pedido_carrito=<?php echo $_GET['id_pedido_carrito']; ?>" method="POST">
                    <div class="form-group">
                        <input type="number" name="cantidad" class="form-control" placeholder="Cantidad" value="<?php echo $cantidad; ?>" required>
                        <select name="estado_pedido" class="form-control">
                            <option value="pendiente" <?php echo ($estado_pedido == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="completado" <?php echo ($estado_pedido == 'completado') ? 'selected' : ''; ?>>Completado</option>
                            <option value="cancelado" <?php echo ($estado_pedido == 'cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                        <input type="date" name="fecha_pedido" class="form-control" placeholder="Fecha del Pedido" value="<?php echo $fecha_pedido; ?>" required>
                        <input type="number" name="id_producto" class="form-control" placeholder="Producto" value="<?php echo $id_producto; ?>" required>
                        <input type="number" name="id_pago" class="form-control" placeholder="Pago" value="<?php echo $id_pago; ?>" required>
                        <input type="number" name="id_comprador" class="form-control" placeholder="Comprador" value="<?php echo $id_comprador; ?>" required>
                    </div>
                    <button class="btn btn-success" name="actualizar">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('../includes/footer.php'); ?>
