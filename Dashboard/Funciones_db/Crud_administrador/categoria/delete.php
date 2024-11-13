<?php
include('../includes/db.php');

if (isset($_GET['id_categoria'])) {
    $id_categoria = $_GET['id_categoria'];
    $query = "DELETE FROM categoria WHERE id_categoria = $id_categoria";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error al eliminar la categoría");
    }

    $_SESSION['message'] = 'Categoría eliminada correctamente';
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php');
}
?>
