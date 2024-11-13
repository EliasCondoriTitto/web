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
        $_SESSION['message'] = 'No tienes permiso para editar este producto';
        $_SESSION['message_type'] = 'danger';
        header("Location: index.php");
        exit();
    }

    $query = "SELECT p.*, e.id_almacen FROM producto p 
              LEFT JOIN esta e ON p.id_producto = e.IdProducto 
              WHERE p.id_producto = $id_producto";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $nombre = $row['nombre'];
        $caracteristica = $row['caracteristica'];
        $precio = $row['precio'];
        $stock = $row['stock'];
        $id_comunario = $row['id_comunario'];
        $id_categoria = $row['id_categoria'];
        $id_almacen = $row['id_almacen'];
    }
}

if (isset($_POST['actualizar'])) {
    $id_producto = $_GET['id_producto'];
    
    if (!canManageProduct($conn, $user_id, $id_producto)) {
        $_SESSION['message'] = 'No tienes permiso para actualizar este producto';
        $_SESSION['message_type'] = 'danger';
        header("Location: index.php");
        exit();
    }
    
    $nombre = $_POST['nombre'];
    $caracteristica = $_POST['caracteristica'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $id_categoria = $_POST['id_categoria'];
    $id_almacen = $_POST['id_almacen'];
    $fecha_actualizacion = date("Y-m-d H:i:s");

    $query = "UPDATE producto SET nombre = '$nombre', caracteristica = '$caracteristica', 
              precio = '$precio', stock = '$stock', id_categoria = '$id_categoria', 
              fecha_actualizacion = '$fecha_actualizacion' WHERE id_producto = $id_producto";
    mysqli_query($conn, $query);

    // Actualizar la relación con el almacén
    $query_esta = "INSERT INTO esta (IdProducto, id_almacen) VALUES ($id_producto, $id_almacen)
                   ON DUPLICATE KEY UPDATE id_almacen = $id_almacen";
    mysqli_query($conn, $query_esta);

    $_SESSION['message'] = 'Producto actualizado correctamente';
    $_SESSION['message_type'] = 'success';
    header("Location: index.php");
}
?>

<?php include('../includes/header.php'); ?>
<div class="container p-4">
    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card card-body">
                <form action="edit.php?id_producto=<?php echo $_GET['id_producto']; ?>" method="POST">
                    <div class="form-group">
                        <input type="text" name="nombre" class="form-control" value="<?php echo $nombre; ?>" required>
                        <textarea name="caracteristica" class="form-control" required><?php echo $caracteristica; ?></textarea>
                        <input type="number" name="precio" class="form-control" value="<?php echo $precio; ?>" required>
                        <input type="number" name="stock" class="form-control" value="<?php echo $stock; ?>" required>

                        <select name="id_categoria" class="form-control" required>
                            <option value="">Selecciona una categoría</option>
                            <?php
                            $query_categoria = "SELECT id_categoria, nombre_categoria FROM categoria";
                            $result_categoria = mysqli_query($conn, $query_categoria);
                            while ($categoria = mysqli_fetch_assoc($result_categoria)) {
                                $selected = ($id_categoria == $categoria['id_categoria']) ? "selected" : "";
                                echo "<option value='{$categoria['id_categoria']}' $selected>{$categoria['nombre_categoria']}</option>";
                            }
                            ?>
                        </select>

                        <select name="id_almacen" class="form-control" required>
                            <option value="">Selecciona un almacén</option>
                            <?php
                            $query_almacen = "SELECT id_almacen, nombre FROM almacen";
                            $result_almacen = mysqli_query($conn, $query_almacen);
                            while ($almacen = mysqli_fetch_assoc($result_almacen)) {
                                $selected = ($id_almacen == $almacen['id_almacen']) ? "selected" : "";
                                echo "<option value='{$almacen['id_almacen']}' $selected>{$almacen['nombre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button class="btn btn-success" name="actualizar">
                        Actualizar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>