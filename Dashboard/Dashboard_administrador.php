<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administrador</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link href="css/styles.css" rel="stylesheet"> <!-- Tu archivo CSS existente -->
    <link href="css/dashboard.css" rel="stylesheet"> <!-- Nuevo archivo CSS -->
    
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_administrador.php">E-Commerce Artesanal</a>
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
                            <li><a class="dropdown-item" href="?seccion=usuarios">Gestión de Usuarios</a></li>
                            <li><a class="dropdown-item" href="?seccion=almacen">Gestión de Almacén</a></li>
                            <li><a class="dropdown-item" href="?seccion=departamentos">Gestión de Departamentos</a></li>
                            <li><a class="dropdown-item" href="?seccion=comunidades">Gestión de Comunidades</a></li>
                            <li><a class="dropdown-item" href="?seccion=empresaD">Gestión de Empresas Delivery</a></li>
                            <li><a class="dropdown-item" href="?seccion=categoriaP">Gestión de Categorías</a></li>
                            <li><a class="dropdown-item" href="?seccion=productos">Gestión de Productos</a></li>
                            <li><a class="dropdown-item" href="?seccion=pedidos">Gestión de Pedidos</a></li>
                        </ul>
                    </li>
                    <!-- Reportes puede estar fuera del menú desplegable -->
                    <li class="nav-item">
                        <a class="nav-link" href="?seccion=reportes">Reportes</a>
                    </li>
                </ul>
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

    <main>
        <?php
        // Incluir el archivo que contiene las funciones de cada sección
        include('Funciones_db/functions_administrador.php');

        // Comprobar la sección seleccionada
        if (isset($_GET['seccion'])) {
            $seccion = $_GET['seccion'];
            switch ($seccion) {
                case 'usuarios':
                    mostrarUsuarios();
                    echo '<a href="Funciones_db/Crud_administrador/usuarios/index.php" class="btn btn-custom mt-3">Gestión de Usuarios</a>';
                    break;
                case 'almacen':
                    mostrarAlmacen();
                    echo '<a href="Funciones_db/Crud_administrador/almacen/index.php" class="btn btn-custom mt-3">Gestión de Almacen</a>';
                    break;
                case 'departamentos':
                    mostrarDepartamentos();
                    echo '<a href="Funciones_db/Crud_administrador/departamento/index.php" class="btn btn-custom mt-3">Gestión de Departamentos</a>';
                    break;
                case 'comunidades':
                    mostrarComunidades();
                    echo '<a href="Funciones_db/Crud_administrador/comunidades/index.php" class="btn btn-custom mt-3">Gestión de Comunidades</a>';
                    break;
                case 'empresaD':
                    mostrarEmpresaD();
                    echo '<a href="Funciones_db/Crud_administrador/empresa_delivery/index.php" class="btn btn-custom mt-3">Gestión de Empresa</a>';
                    break;
                case 'categoriaP':
                    mostrarCategoriaP();
                    echo '<a href="Funciones_db/Crud_administrador/categoria/index.php" class="btn btn-custom mt-3">Gestión de Categoria</a>';
                    break;
                case 'productos':
                    mostrarProductos();
                    echo '<a href="Funciones_db/Crud_administrador/productos/index.php" class="btn btn-custom mt-3">Gestión de Productos</a>';
                    break;        
                case 'reportes':
                    mostrarReportes();
                    echo '<a href="Funciones_db/Crud_administrador/reportes/index.php" class="btn btn-custom mt-3">Gestión de Reportes</a>';
                    break;
                case 'pedidos':
                    mostrarPedidos();
                    echo '<a href="Funciones_db/Crud_administrador/pedidos/index.php" class="btn btn-custom mt-3">Gestión de Pedidos</a>';
                    break;
                default:
                    echo "<h2>Bienvenido al Dashboard del Administrador</h2>";
                    break;
            }
        } else {
            echo "<h2>Bienvenido al Dashboard del Administrador</h2>";
        }
        
        ?>
    </main>
    
    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>
