<?php
include("../includes/db.php");

$nombre_categoria = '';
$descripcion = '';

if (isset($_GET['id_categoria'])) {
    $id_categoria = $_GET['id_categoria'];
    $query = "SELECT * FROM categoria WHERE id_categoria = $id_categoria";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $nombre_categoria = $row['nombre_categoria'];
        $descripcion = $row['descripcion'];
    }
}

if (isset($_POST['update'])) {
    $id_categoria = $_GET['id_categoria'];
    $nombre_categoria = $_POST['nombre_categoria'];
    $descripcion = $_POST['descripcion'];

    $query = "UPDATE categoria SET nombre_categoria = '$nombre_categoria', descripcion = '$descripcion' WHERE id_categoria = $id_categoria";
    mysqli_query($conn, $query);

    $_SESSION['message'] = 'Categoría actualizada con éxito';
    $_SESSION['message_type'] = 'warning';
    header('Location: index.php');
}
?>

<?php include('../includes/header.php'); ?>

<div class="container p-4">
    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card card-body">
                <form action="edit.php?id_categoria=<?php echo $_GET['id_categoria']; ?>" method="POST">
                    <div class="form-group">
                        <p>
                            <input type="text" name="nombre_categoria" class="form-control" value="<?php echo $nombre_categoria; ?>" autofocus>
                        </p>
                        <p>
                            <textarea name="descripcion" class="form-control"><?php echo $descripcion; ?></textarea>
                        </p>
                    </div>
                    <button class="btn btn-success" name="update">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
