<?php include('../includes/db.php'); ?>
<?php include('../includes/header.php'); ?>

<main class="container p-4">
    <div class="row">
        <div class="col-md-4">
            <?php if (isset($_SESSION['message'])) { ?>
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
                        <p><input type="text" name="nombre" class="form-control" placeholder="Nombre" autofocus></p>
                        <p><input type="text" name="apellido" class="form-control" placeholder="Apellido" autofocus></p>
                        <p><input type="text" name="correo" class="form-control" placeholder="Correo Electrónico" autofocus></p>
                        <p><input type="password" name="contraseña" class="form-control" placeholder="Contraseña" required></p>
                        <p><input type="date" name="fecha_naci" class="form-control" placeholder="Fecha de Nacimiento"></p>
                        <p><input type="text" name="telefono" class="form-control" placeholder="Teléfono"></p>
                        
                        <!-- Rol y selección de comunidad o empresa -->
                        <p>
                            <select name="rol" class="form-control" id="rol" onchange="showFields(this.value)">
                                <option value="">Selecciona un rol</option>
                                <option value="administrador">Administrador</option>
                                <option value="comprador">Comprador</option>
                                <option value="vendedor">Vendedor (Comunario)</option>
                                <option value="delivery">Delivery</option>
                            </select>
                        </p>

                        <!-- Campo de Comunidad -->
                        <div id="comunidadField" style="display:none;">
                            <select name="id_comunidad" class="form-control">
                                <option value="">Selecciona una comunidad</option>
                                <?php
                                $query_comunidad = "SELECT id_comunidad, nombre FROM comunidad";
                                $result_comunidad = mysqli_query($conn, $query_comunidad);
                                while ($comunidad = mysqli_fetch_assoc($result_comunidad)) {
                                    echo "<option value='{$comunidad['id_comunidad']}'>{$comunidad['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Campo de Empresa de Delivery -->
                        <div id="deliveryField" style="display:none;">
                            <select name="id_empresa" class="form-control">
                                <option value="">Selecciona una empresa de delivery</option>
                                <?php
                                $query_empresa = "SELECT id_empresa, nombre FROM empresa";
                                $result_empresa = mysqli_query($conn, $query_empresa);
                                while ($empresa = mysqli_fetch_assoc($result_empresa)) {
                                    echo "<option value='{$empresa['id_empresa']}'>{$empresa['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <input type="submit" class="btn btn-success btn block" name="save" value="Enviar">
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <table class="table table-border">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Correo Electrónico</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Teléfono</th>
                        <th>Rol</th>
                        <th>Fecha de Registro</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Consultar usuarios con comunidad o empresa asociada
                    $query = "SELECT u.*, c.nombre AS nombre_comunidad, e.nombre AS nombre_empresa 
                              FROM usuario u 
                                LEFT JOIN comunario co ON u.id_usuario = co.id_comunario
                                LEFT JOIN comunidad c ON co.id_comunidad = c.id_comunidad
                                LEFT JOIN delivery d ON u.id_usuario = d.id_delivery
                                LEFT JOIN empresa e ON d.id_empresa = e.id_empresa";
                    $result_usuario = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result_usuario)) { ?>
                        <tr>
                            <td><?php echo $row['nombre']; ?></td>
                            <td><?php echo $row['apellido']; ?></td>
                            <td><?php echo $row['correo']; ?></td>
                            <td><?php echo $row['fecha_naci']; ?></td>
                            <td><?php echo $row['telefono']; ?></td>
                            <td><?php echo $row['rol']; ?></td>
                            <td><?php echo $row['fecha_registro']; ?></td>
                            <td>
                                <a href="edit.php?id_usuario=<?php echo $row['id_usuario']; ?>" class="btn btn-secondary">Editar</a>
                                <a href="delete.php?id_usuario=<?php echo $row['id_usuario']; ?>" class="btn btn-danger">Eliminar</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    function showFields(value) {
        document.getElementById('comunidadField').style.display = value === 'vendedor' ? 'block' : 'none';
        document.getElementById('deliveryField').style.display = value === 'delivery' ? 'block' : 'none';
    }
</script>

<?php include('../includes/footer.php'); ?>
