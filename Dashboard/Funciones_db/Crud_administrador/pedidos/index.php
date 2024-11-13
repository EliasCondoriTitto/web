<?php include('../includes/db.php'); ?>
<?php include('../includes/header.php'); ?>

<main class="container p-4">
    <div class="row">

        <div class="col-md-4">
            <?php if(isset ($_SESSION['message'])) { ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php session_unset(); } ?>

            <div class="card card-body">
                <form action="save.php" method="POST">
                    <div class="form-group mb-3">
                        <input type="number" name="cantidad" class="form-control" placeholder="Cantidad" required autofocus>
                    </div>
                    <div class="form-group mb-3">
                        <select name="estado_pedido" class="form-control" required>
                            <option value="">Selecciona un estado</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="completado">Completado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <input type="date" name="fecha_pedido" class="form-control" placeholder="Fecha del Pedido" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="number" name="id_producto" class="form-control" placeholder="Producto" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="number" name="id_pago" class="form-control" placeholder="Pago" required>
                    </div>
                    <div class="form-group mb-3">
                        <input type="number" name="id_comprador" class="form-control" placeholder="Comprador" required>
                    </div>
                    <input type="submit" class="btn btn-success btn-block" name="save" value="Guardar Pedido">
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Estado del Pedido</th>
                        <th>Fecha del Pedido</th>
                        <th>Id del Producto</th>
                        <th>Id del Pago</th>
                        <th>Id del Comprador</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM pedido_carrito";
                    $result_pedido_carrito = mysqli_query($conn, $query);
                    while($row = mysqli_fetch_assoc($result_pedido_carrito)) { ?>
                        <tr>
                            <td><?php echo $row['cantidad']; ?></td>
                            <td><?php echo $row['estado_pedido']; ?></td>
                            <td><?php echo $row['fecha_pedido']; ?></td>
                            <td><?php echo $row['id_producto']; ?></td>
                            <td><?php echo $row['id_pago']; ?></td>
                            <td><?php echo $row['id_comprador']; ?></td>
                            <td>
                                <a href="edit.php?id_pedido_carrito=<?php echo $row['id_pedido_carrito'] ?>" class="btn btn-secondary">
                                    Editar
                                </a>
                                <a href="delete.php?id_pedido_carrito=<?php echo $row['id_pedido_carrito'] ?>" class="btn btn-danger">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
