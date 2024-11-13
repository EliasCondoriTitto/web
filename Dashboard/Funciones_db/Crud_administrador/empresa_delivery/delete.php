<?php
include("../includes/db.php");

if (isset($_GET['id_empresa'])) {
    $id_empresa = $_GET['id_empresa'];
    $query = "DELETE FROM empresa WHERE id_empresa = $id_empresa";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Error al eliminar empresa.");
    }

    $_SESSION['message'] = "Empresa eliminada correctamente.";
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php');
}
?>
