<?php
include("../includes/db.php");

$nombre = '';
$direccion = '';

if (isset($_GET['id_empresa'])) {
    $id_empresa = $_GET['id_empresa'];
    $query = "SELECT * FROM empresa WHERE id_empresa = $id_empresa";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $nombre = $row['nombre'];
        $direccion = $row['direccion'];
    }
}

if (isset($_POST['update_empresa'])) {
    $id_empresa = $_GET['id_empresa'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];

    $query = "UPDATE empresa SET nombre = '$nombre', direccion = '$direccion' WHERE id_empresa = $id_empresa";
    mysqli_query($conn, $query);

    $_SESSION['message'] = "Empresa actualizada correctamente.";
    $_SESSION['message_type'] = 'warning';
    header('Location: index.php');
}
?>

<?php include('../includes/header.php'); ?>
<div class="container p-4">
    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card card-body">
                <form action="edit.php?id_empresa=<?php echo $_GET['id_empresa']; ?>" method="POST">
                    <div class="form-group">
                        <input type="text" name="nombre" class="form-control" value="<?php echo $nombre; ?>" placeholder="Nombre de la Empresa">
                    </div>
                    <div class="form-group">
                        <input type="text" name="direccion" class="form-control" value="<?php echo $direccion; ?>" placeholder="Dirección">
                    </div>
                    <button class="btn btn-success" name="update_empresa">Actualizar Empresa</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('../includes/footer.php'); ?>
