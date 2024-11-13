<?php 
include('../includes/db.php');
include('auth_products.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['id_usuario'];
$user_role = getUserRole($conn, $user_id);

include('../includes/header.php'); 
?>

<main class="container p-4">
    <div class="row">
        <div class="col-md-4">
            <?php if (isset($_SESSION['message'])) { ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php session_unset(); } ?>

            <div class="card card-body">
                <form action="save.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <p><input type="text" name="nombre" class="form-control" placeholder="Nombre del Producto" required></p>
                        <p><input type="text" name="caracteristica" class="form-control" placeholder="Características" required></p>
                        <p><input type="number" name="precio" class="form-control" placeholder="Precio" required></p>
                        <p><input type="number" name="stock" class="form-control" placeholder="Stock disponible" required></p>

                        <!-- Selección de la categoría -->
                        <p>
                            <select name="id_categoria" class="form-control" required>
                                <option value="">Selecciona una categoría</option>
                                <?php
                                $query_categoria = "SELECT id_categoria, nombre_categoria FROM categoria";
                                $result_categoria = mysqli_query($conn, $query_categoria);
                                while ($categoria = mysqli_fetch_assoc($result_categoria)) {
                                    echo "<option value='{$categoria['id_categoria']}'>{$categoria['nombre_categoria']}</option>";
                                }
                                ?>
                            </select>
                        </p>

                        <!-- Selección del almacén -->
                        <p>
                            <select name="id_almacen" class="form-control" required>
                                <option value="">Selecciona un almacén</option>
                                <?php
                                $query_almacen = "SELECT id_almacen, nombre FROM almacen";
                                $result_almacen = mysqli_query($conn, $query_almacen);
                                while ($almacen = mysqli_fetch_assoc($result_almacen)) {
                                    echo "<option value='{$almacen['id_almacen']}'>{$almacen['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </p>

                        <?php if ($user_role === 'administrador'): ?>
                        <!-- Selección del comunario (solo para administradores) -->
                        <p>
                            <select name="id_comunario" class="form-control" required>
                                <option value="">Selecciona un comunario</option>
                                <?php
                                $query_comunario = "SELECT u.id_usuario, u.nombre, u.apellido FROM usuario u 
                                                    JOIN comunario c ON u.id_usuario = c.id_comunario";
                                $result_comunario = mysqli_query($conn, $query_comunario);
                                while ($comunario = mysqli_fetch_assoc($result_comunario)) {
                                    echo "<option value='{$comunario['id_usuario']}'>{$comunario['nombre']} {$comunario['apellido']}</option>";
                                }
                                ?>
                            </select>
                        </p>
                        <?php else: ?>
                            <input type="hidden" name="id_comunario" value="<?php echo $user_id; ?>">
                        <?php endif; ?>

                        <!-- Cargar imágenes -->
                        <p><input type="file" name="imagenes[]" class="form-control" multiple></p>
                    </div>
                    <input type="submit" class="btn btn-success btn-block" name="save" value="Guardar producto">
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre del Producto</th>
                        <th>Características</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Fecha de Creación</th>
                        <th>Última Actualización</th>
                        <th>Comunario</th>
                        <th>Categoría</th>
                        <th>Almacén</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result_producto = getProductsForUser($conn, $user_id);
                    while ($row = mysqli_fetch_assoc($result_producto)) { ?>
                        <tr>
                            <td><?php echo $row['nombre']; ?></td>
                            <td><?php echo $row['caracteristica']; ?></td>
                            <td><?php echo $row['precio']; ?></td>
                            <td><?php echo $row['stock']; ?></td>
                            <td><?php echo $row['fecha_creacion']; ?></td>
                            <td><?php echo $row['fecha_actualizacion']; ?></td>
                            <td><?php echo $row['nombre_comunario'] . " " . $row['apellido_comunario']; ?></td>
                            <td><?php echo $row['nombre_categoria']; ?></td>
                            <td><?php echo $row['nombre_almacen']; ?></td>
                            <td>
                                <a href="edit.php?id_producto=<?php echo $row['id_producto']; ?>" class="btn btn-secondary">Editar</a>
                                <a href="delete.php?id_producto=<?php echo $row['id_producto']; ?>" class="btn btn-danger">Eliminar</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>