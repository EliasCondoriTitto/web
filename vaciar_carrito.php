<?php
session_start();
unset($_SESSION['carrito']); // Eliminar los productos del carrito
header("Location: carrito.php");
exit();
