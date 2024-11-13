<?php
// Conexión a la base de datos MySQL
$servername = "sql103.infinityfree.com"; // Cambia si es necesario
$username = "if0_37706422"; // Usuario de la BD
$password = "esx9Vdd8an"; // Contraseña de la BD
$database = "if0_37706422_artesaniasbolivia"; // Nombre de tu base de datos

// Crear conexión
$conn = mysqli_connect($servername, $username, $password, $database);

// Verificar conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}
?>
