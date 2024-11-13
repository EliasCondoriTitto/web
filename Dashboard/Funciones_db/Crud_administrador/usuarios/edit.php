<?php
include("../includes/db.php");

$nombre = '';
$apellido = '';
$correo = '';
$fecha_naci = '';
$telefono = '';
$rol = '';
$id_comunidad = '';
$id_empresa = '';

if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];

    // Consultar los datos del usuario
    $query = "SELECT * FROM usuario WHERE id_usuario = $id_usuario";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $nombre = $row['nombre'];
        $apellido = $row['apellido'];
        $correo = $row['correo'];
        $fecha_naci = $row['fecha_naci'];
        $telefono = $row['telefono'];
        $rol = $row['rol'];
    }
}

if (isset($_POST['actualizar'])) {
    $id_usuario = $_GET['id_usuario'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $fecha_naci = $_POST['fecha_naci'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];
    $id_comunidad = isset($_POST['id_comunidad']) ? $_POST['id_comunidad'] : null;
    $id_empresa = isset($_POST['id_empresa']) ? $_POST['id_empresa'] : null;

    // Actualizar los datos del usuario
    $query = "UPDATE usuario SET nombre = '$nombre', apellido = '$apellido', correo = '$correo', 
              fecha_naci = '$fecha_naci', telefono = '$telefono', rol = '$rol'
              WHERE id_usuario = $id_usuario";

    mysqli_query($conn, $query);

    $_SESSION['message'] = 'Datos actualizados con éxito';
    header('Location: index.php');
}
?>

<?php include('../includes/header.php'); ?>
<div class="container p-4">
    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card card-body">
                <form action="edit.php?id_usuario=<?php echo $_GET['id_usuario']; ?>" method="POST">
                    <div class="form-group">
                        <p><input type="text" name="nombre" class="form-control" placeholder="Nombre" value="<?php echo $nombre; ?>"></p>
                        <p><input type="text" name="apellido" class="form-control" placeholder="Apellido" value="<?php echo $apellido; ?>"></p>
                        <p><input type="text" name="correo" class="form-control" placeholder="Correo" value="<?php echo $correo; ?>"></p>
                        <p><input type="date" name="fecha_naci" class="form-control" placeholder="Fecha de Nacimiento" value="<?php echo $fecha_naci; ?>"></p>
                        <p><input type="text" name="telefono" class="form-control" placeholder="Teléfono" value="<?php echo $telefono; ?>"></p>

                        <select name="rol" class="form-control" id="rol" onchange="showFields(this.value)">
                            <option value="">Selecciona un rol</option>
                            <option value="administrador" <?php if($rol === 'administrador') echo 'selected'; ?>>Administrador</option>
                            <option value="comprador" <?php if($rol === 'comprador') echo 'selected'; ?>>Comprador</option>
                            <option value="vendedor" <?php if($rol === 'vendedor') echo 'selected'; ?>>Vendedor (Comunario)</option>
                            <option value="delivery" <?php if($rol === 'delivery') echo 'selected'; ?>>Delivery</option>
                        </select>

                        <div id="comunidadField" style="display: none;">
                            <select name="id_comunidad" class="form-control">
                                <option value="">Selecciona una comunidad</option>
                                <?php
                                $query_comunidad = "SELECT id_comunidad, nombre FROM comunidad";
                                $result_comunidad = mysqli_query($conn, $query_comunidad);
                                while ($comunidad = mysqli_fetch_assoc($result_comunidad)) {
                                    $selected = ($id_comunidad == $comunidad['id_comunidad']) ? 'selected' : '';
                                    echo "<option value='{$comunidad['id_comunidad']}' $selected>{$comunidad['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div id="deliveryField" style="display: none;">
                            <select name="id_empresa" class="form-control">
                                <option value="">Selecciona una empresa de delivery</option>
                                <?php
                                $query_empresa = "SELECT id_empresa, nombre FROM empresa";
                                $result_empresa = mysqli_query($conn, $query_empresa);
                                while ($empresa = mysqli_fetch_assoc($result_empresa)) {
                                    $selected = ($id_empresa == $empresa['id_empresa']) ? 'selected' : '';
                                    echo "<option value='{$empresa['id_empresa']}' $selected>{$empresa['nombre']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-success" name="actualizar">
                        Actualizar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showFields(value) {
        document.getElementById('comunidadField').style.display = value === 'vendedor' ? 'block' : 'none';
        document.getElementById('deliveryField').style.display = value === 'delivery' ? 'block' : 'none';
    }
</script>

<?php include('../includes/footer.php'); ?>
