<?php 
include("../includes/db.php");

if (isset($_POST['save'])) {
    $nombre_categoria = $_POST['nombre_categoria'];
    $descripcion = $_POST['descripcion'];

    $query = "INSERT INTO categoria (nombre_categoria, descripcion) VALUES ('$nombre_categoria', '$descripcion')";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error al guardar la categoría");
    }

    $_SESSION['message'] = 'Categoría guardada con éxito';
    $_SESSION['message_type'] = 'success';

    header("Location: index.php");
}
?>
