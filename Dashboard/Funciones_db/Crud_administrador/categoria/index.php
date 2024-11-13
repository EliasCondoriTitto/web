<?php include('../includes/db.php'); ?>
<?php include('../includes/header.php'); ?>

<main class="container p-4">
    <div class="row">

        <div class="col-md-4">

            <?php if(isset($_SESSION['message'])) { ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php session_unset(); } ?>

            <div class="card card-body">
                <form action="save.php" method="POST">
                    <div class="form-group">
                        <p>
                            <input type="text" name="nombre_categoria" class="form-control" placeholder="Nombre de la Categoría" autofocus>
                        </p>
                        <p>
                            <textarea name="descripcion" class="form-control" placeholder="Descripción"></textarea>
                        </p>
                    </div>
                    <input type="submit" class="btn btn-success btn-block" name="save" value="Guardar Categoría">
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <table class="table table-border">
                <thead>
                    <tr>
                        <th>Nombre Categoría</th>
                        <th>Descripción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM categoria";
                    $result_categoria = mysqli_query($conn, $query);

                    while($row = mysqli_fetch_assoc($result_categoria)) { ?>
                        <tr>
                            <td><?php echo $row['nombre_categoria']; ?></td>
                            <td><?php echo $row['descripcion']; ?></td>
                            <td>
                                <a href="edit.php?id_categoria=<?php echo $row['id_categoria'] ?>" class="btn btn-secondary">Editar</a>
                                <a href="delete.php?id_categoria=<?php echo $row['id_categoria'] ?>" class="btn btn-danger">Eliminar</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<?php include('../includes/footer.php'); ?>
