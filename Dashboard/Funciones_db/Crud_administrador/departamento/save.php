<?php 
include("../includes/db.php");

if (isset($_POST['save'])) { 
    $nombre_departamento = $_POST['nombre_departamento'];
    $capital = $_POST['capital'];
    $superficie = $_POST['superficie'];

    $query = "INSERT INTO departamento(nombre_departamento, capital, superficie) 
              VALUES ('$nombre_departamento', '$capital', '$superficie')";
    
    $result = mysqli_query($conn, $query); 

    if (!$result) { 
        die("Error: " . mysqli_error($conn));
    }
    
    $_SESSION['message'] = "Departamento guardado exitosamente.";
    $_SESSION['message_type'] = 'success';

    header("Location: index.php");
}
?>
