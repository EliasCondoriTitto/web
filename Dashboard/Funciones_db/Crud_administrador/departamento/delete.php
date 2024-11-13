<?php
include('../includes/db.php');

if (isset($_GET['nombre_departamento'])) {
    $nombre_departamento = $_GET['nombre_departamento'];
    $query = "DELETE FROM departamento WHERE nombre_departamento = '$nombre_departamento'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error al eliminar departamento.");
    }

    $_SESSION['message'] = 'Departamento eliminado correctamente.';
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php');
}
?>
