<?php 
include("../includes/db.php");

if (isset($_POST['save_empresa'])) {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];

    $query = "INSERT INTO empresa (nombre, direccion) VALUES ('$nombre', '$direccion')";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error al guardar empresa.");
    }

    $_SESSION['message'] = "Empresa guardada correctamente.";
    $_SESSION['message_type'] = 'success';

    header("Location: index.php");
}
?>
