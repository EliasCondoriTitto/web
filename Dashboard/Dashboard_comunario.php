<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Comunario</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/dashboard.css" rel="stylesheet"> <!-- Añade un archivo específico para estilos del dashboard -->
    
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_comunario.php">E-Commerce Artesanal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Dropdown menú de gestión -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="gestionesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Gestiones
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="gestionesDropdown">
                            <li><a class="dropdown-item" href="?seccion=productos">Gestión de Productos</a></li>
                            <li><a class="dropdown-item" href="?seccion=ventas">Gestión de Ventas</a></li>
                            <li><a class="dropdown-item" href="?seccion=perfil">Perfil de Comunidad</a></li>
                        </ul>
                    </li>
                </ul>
                <!-- Usuario autenticado -->
                <!-- Usuario autenticado -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php"><i class="fas fa-sign-out-alt"></i> Salir del Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Mensajes de sesión -->
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show m-4" role="alert">
            <?= $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php session_unset(); ?>
    <?php endif; ?>

    <main class="container my-4">
        <?php
        // Incluir el archivo que contiene las funciones de comunario
        include('Funciones_db/functions_comunario.php');

        // Determinar la sección seleccionada
        if (isset($_GET['seccion'])) {
            $seccion = $_GET['seccion'];
            switch ($seccion) {
                case 'productos':
                    mostrarProductosComunario();
                    echo '<a href="Funciones_db/Crud_administrador/productos/index.php" class="btn btn-custom mt-3">Gestionar Productos</a>';
                    break;
                case 'ventas':
                    mostrarVentasComunario();
                    echo '<a href="Funciones_db/Crud_administrador/ventas/index.php" class="btn btn-custom mt-3">Gestionar Ventas</a>';
                    break;
                case 'perfil':
                    mostrarPerfilComunidad();
                    echo '<a href="Funciones_db/Crud_administrador/perfil/index.php" class="btn btn-custom mt-3">Ver Perfil</a>';
                    break;
                default:
                    echo "<h2>Bienvenido al Dashboard del Comunario</h2>";
                    break;
            }
        } else {
            echo "<h2>Bienvenido al Dashboard del Comunario</h2>";
        }
        ?>
    </main>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>
