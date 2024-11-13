<?php 
include("../includes/db.php");

if(isset($_POST['save'])) {
    $nombre = $_POST['nombre'];  
    $nro_habitantes = $_POST['nro_habitantes'];
    $nombre_departamento = $_POST['nombre_departamento'];

    $query = "INSERT INTO comunidad(nombre, nro_habitantes, nombre_departamento) 
              VALUES ('$nombre', '$nro_habitantes', '$nombre_departamento')";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error al guardar la comunidad: " . mysqli_error($conn));
    }

    $_SESSION['message'] = "Comunidad guardada con éxito.";
    $_SESSION['message_type'] = 'success';

    header("Location: index.php");
}
?>
