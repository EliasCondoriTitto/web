<?php
include('../includes/db.php');

if (isset($_GET['id_comunidad'])) {
    $id_comunidad = $_GET['id_comunidad'];
    $query = "DELETE FROM comunidad WHERE id_comunidad = $id_comunidad";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error al eliminar la comunidad: " . mysqli_error($conn));
    }

    $_SESSION['message'] = 'Comunidad eliminada con éxito';
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php');
}
?>
