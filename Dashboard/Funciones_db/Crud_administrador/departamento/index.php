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
                    <div class="form-group">
                        <p>
                            <input type="text" name="nombre_departamento" class="form-control" placeholder="Nombre del Departamento" required autofocus>
                        </p>
                        <p>
                            <input type="text" name="capital" class="form-control" placeholder="Capital" required>
                        </p>
                        <p>
                            <input type="number" name="superficie" class="form-control" placeholder="Superficie (km²)" required>
                        </p>
                    </div>
                    <input type="submit" class="btn btn-success btn block" name="save" value="Guardar Departamento">
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <table class="table table-border">
                <thead>
                    <tr>
                        <th>Nombre Departamento</th>
                        <th>Capital</th>
                        <th>Superficie (km²)</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM departamento";
                    $result_departamento = mysqli_query($conn, $query);
                    while($row = mysqli_fetch_assoc($result_departamento)) { ?>
                        <tr>
                            <td><?php echo $row['nombre_departamento']; ?></td>
                            <td><?php echo $row['capital']; ?></td>
                            <td><?php echo $row['superficie']; ?></td>
                            <td>
                                <a href="edit.php?nombre_departamento=<?php echo $row['nombre_departamento'] ?>" class="btn btn-secondary">
                                    Editar
                                </a>
                                <a href="delete.php?nombre_departamento=<?php echo $row['nombre_departamento'] ?>" class="btn btn-danger">
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
