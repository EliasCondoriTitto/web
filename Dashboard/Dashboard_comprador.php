
<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php"); // Redirige al login si no está autenticado
    exit();
}

// Obtener información adicional del usuario si es necesario

$id_comprador = $_SESSION['id_usuario'];

?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Comprador</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_comprador.php">Comprador - E-Commerce Artesanal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="?seccion=productos"><i class="fas fa-box-open"></i> Ver Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?seccion=historial"><i class="fas fa-history"></i> Historial de Compras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?seccion=pedidos"><i class="fas fa-shopping-cart"></i> Mis Pedidos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?seccion=resenas"><i class="fas fa-star"></i> Mis Reseñas</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php"><i class="fas fa-sign-out-alt"></i> Salir del Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        <?php
        include('Funciones_db/functions_comprador.php'); 

        if (isset($_GET['seccion'])) {
            $seccion = $_GET['seccion'];
            switch ($seccion) {
                case 'productos':
                    verProductos();
                    break;
                case 'historial':
                    historialCompras($id_comprador);
                    break;
                case 'pedidos':
                    misPedidos($id_comprador);
                    break;
                case 'resenas':
                    misResenas($id_comprador);
                    break;
                default:
                    echo "<h2>Bienvenido al Dashboard del Comprador</h2>";
                    break;
            }
        } else {
            echo "<h2>Bienvenido al Dashboard del Comprador</h2>";
        }
        ?>
    </main>

    <!-- Footer -->
    <?php include 'css/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>
