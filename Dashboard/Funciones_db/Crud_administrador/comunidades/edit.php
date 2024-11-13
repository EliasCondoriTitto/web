<?php
include("../includes/db.php");

$nombre = '';
$nro_habitantes = '';
$nombre_departamento = '';

if (isset($_GET['id_comunidad'])) {
    $id_comunidad = $_GET['id_comunidad'];
    $query = "SELECT * FROM comunidad WHERE id_comunidad = $id_comunidad";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $nombre = $row['nombre'];
        $nro_habitantes = $row['nro_habitantes'];
        $nombre_departamento = $row['nombre_departamento'];
    }
}

if (isset($_POST['actualizar'])) {
    $id_comunidad = $_GET['id_comunidad'];
    $nombre = $_POST['nombre'];
    $nro_habitantes = $_POST['nro_habitantes'];
    $nombre_departamento = $_POST['nombre_departamento'];

    $query = "UPDATE comunidad SET nombre = '$nombre', nro_habitantes = '$nro_habitantes', nombre_departamento = '$nombre_departamento' WHERE id_comunidad = $id_comunidad";
    mysqli_query($conn, $query);

    $_SESSION['message'] = 'Comunidad actualizada con éxito';
    $_SESSION['message_type'] = 'warning';
    
    header('Location: index.php');
}
?>

<?php include('../includes/header.php'); ?>
<div class="container p-4">
    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card card-body">
                <form action="edit.php?id_comunidad=<?php echo $_GET['id_comunidad']; ?>" method="POST">
                    <div class="form-group">
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre de la Comunidad" value="<?php echo $nombre; ?>" required>
                        <input type="number" name="nro_habitantes" class="form-control" placeholder="Número de Habitantes" value="<?php echo $nro_habitantes; ?>" required>
                        <select name="nombre_departamento" class="form-control">
                            <?php
                                $query = "SELECT nombre_departamento FROM departamento";
                                $result_departamento = mysqli_query($conn, $query);
                                while($row = mysqli_fetch_assoc($result_departamento)) {
                                    $selected = ($nombre_departamento == $row['nombre_departamento']) ? 'selected' : '';
                                    echo "<option value='{$row['nombre_departamento']}' $selected>{$row['nombre_departamento']}</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <button class="btn btn-success" name="actualizar">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('../includes/footer.php'); ?>
