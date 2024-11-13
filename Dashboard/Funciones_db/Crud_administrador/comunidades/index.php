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
                            <input type="text" name="nombre" class="form-control" placeholder="Nombre de la Comunidad" required autofocus>
                        </p>
                        <p>
                            <input type="number" name="nro_habitantes" class="form-control" placeholder="Número de Habitantes" required>
                        </p>
                        <p>
                            <select name="nombre_departamento" class="form-control" required>
                                <option value="">Selecciona un Departamento</option>
                                <?php
                                    $query = "SELECT nombre_departamento FROM departamento";
                                    $result_departamento = mysqli_query($conn, $query);
                                    while($row = mysqli_fetch_assoc($result_departamento)) {
                                        echo "<option value='{$row['nombre_departamento']}'>{$row['nombre_departamento']}</option>";
                                    }
                                ?>
                            </select>
                        </p>
                    </div>
                    <input type="submit" class="btn btn-success btn block" name="save" value="Guardar Comunidad">
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre Comunidad</th>
                        <th>Número de Habitantes</th>
                        <th>Departamento</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM comunidad";
                    $result_comunidad = mysqli_query($conn, $query);
                    while($row = mysqli_fetch_assoc($result_comunidad)) { ?>
                        <tr>
                            <td><?php echo $row['nombre']; ?></td>
                            <td><?php echo $row['nro_habitantes']; ?></td>
                            <td><?php echo $row['nombre_departamento']; ?></td>
                            <td>
                                <a href="edit.php?id_comunidad=<?php echo $row['id_comunidad'] ?>" class="btn btn-secondary">
                                    Editar
                                </a>
                                <a href="delete.php?id_comunidad=<?php echo $row['id_comunidad'] ?>" class="btn btn-danger">
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
