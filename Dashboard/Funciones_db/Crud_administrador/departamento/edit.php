<?php
include("../includes/db.php");

$nombre_departamento = '';
$capital = '';
$superficie = '';

// Verificar si se ha recibido el parámetro 'nombre_departamento' en la URL
if (isset($_GET['nombre_departamento'])) {
    $nombre_departamento = $_GET['nombre_departamento'];
    
    // Consulta para obtener los datos del departamento
    $query = "SELECT * FROM departamento WHERE nombre_departamento = '$nombre_departamento'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $capital = $row['capital'];
        $superficie = $row['superficie'];
    }
}

// Verificar si se ha enviado el formulario de actualización
if (isset($_POST['actualizar'])) {
    $nombre_departamento = $_POST['nombre_departamento'];
    $capital = $_POST['capital'];
    $superficie = $_POST['superficie'];
    
    // Consulta para actualizar los datos del departamento
    $query = "UPDATE departamento SET capital = '$capital', superficie = '$superficie' 
              WHERE nombre_departamento = '$nombre_departamento'";
    
    mysqli_query($conn, $query);
    
    $_SESSION['message'] = 'Departamento actualizado exitosamente.';
    $_SESSION['message_type'] = 'warning';
    
    header('Location: index.php');
}
?>

<?php include('../includes/header.php'); ?>
<div class="container p-4">
    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card card-body">
                <form action="edit.php?nombre_departamento=<?php echo $_GET['nombre_departamento']; ?>" method="POST">
                    <div class="form-group">
                        <p>
                            <input type="text" name="nombre_departamento" class="form-control" placeholder="Nombre del Departamento" value="<?php echo $nombre_departamento; ?>" required autofocus>
                        </p>
                        <p>
                            <input type="text" name="capital" class="form-control" placeholder="Capital" value="<?php echo $capital; ?>" required>
                        </p>
                        <p>
                            <input type="number" name="superficie" class="form-control" placeholder="Superficie (km²)" value="<?php echo $superficie; ?>" required>
                        </p>
                    </div>
                    <button class="btn btn-success" name="actualizar">
                        Actualizar Departamento
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('../includes/footer.php'); ?>
