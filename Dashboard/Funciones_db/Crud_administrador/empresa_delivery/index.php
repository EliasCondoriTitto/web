<?php include('../includes/db.php'); ?>
<?php include('../includes/header.php'); ?>

<main class="container p-4">
    <div class="row">
        <div class="col-md-4">
            <?php if(isset($_SESSION['message'])) { ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php session_unset(); } ?>

            <div class="card card-body">
                <form action="save.php" method="POST">
                    <div class="form-group">
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre de la Empresa" required autofocus>
                    </div>
                    <div class="form-group">
                        <input type="text" name="direccion" class="form-control" placeholder="Dirección" required>
                    </div>
                    <input type="submit" class="btn btn-success btn-block" name="save_empresa" value="Guardar Empresa">
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM empresa";
                    $result_empresas = mysqli_query($conn, $query);
                    while($row = mysqli_fetch_assoc($result_empresas)) { ?>
                        <tr>
                            <td><?php echo $row['nombre']; ?></td>
                            <td><?php echo $row['direccion']; ?></td>
                            <td>
                                <a href="edit.php?id_empresa=<?php echo $row['id_empresa'] ?>" class="btn btn-secondary">
                                    Editar
                                </a>
                                <a href="delete.php?id_empresa=<?php echo $row['id_empresa'] ?>" class="btn btn-danger">
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
