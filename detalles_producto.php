<?php
session_start();
require 'db.php'; // Conexión a la base de datos

// Obtener el id del producto desde la URL
$id_producto = isset($_GET['id_producto']) ? (int)$_GET['id_producto'] : 0;

// Verificar si el id_producto es válido
if ($id_producto <= 0) {
    die("Producto no válido.");
}

// Consulta de productos con unión para obtener el nombre del comunario y la comunidad
$consulta = "SELECT P.*, U.nombre AS nombre_comunario, U.apellido AS apellido_comunario, C.nombre AS nombre_comunidad, A.nombre AS nombre_almacen
        FROM PRODUCTO P
        INNER JOIN COMUNARIO CO ON P.id_comunario = CO.id_comunario
        INNER JOIN USUARIO U ON CO.id_comunario = U.id_usuario
        INNER JOIN COMUNIDAD C ON CO.id_comunidad = C.id_comunidad
        INNER JOIN ESTA E ON P.id_producto = E.id_producto
        INNER JOIN ALMACEN A ON E.id_almacen = A.id_almacen
        WHERE P.id_producto = ?";

$stmt = $conn->prepare($consulta);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Producto no encontrado.");
}

$producto = $resultado->fetch_assoc();

// Deserializar las imágenes
$imagenes = unserialize($producto['imagenes']);

// Verificar si se agrega un producto al carrito
if (isset($_POST['agregar_carrito'])) {
    $id_producto = $_POST['id_producto'];
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
    $detalles = isset($_POST['detalles']) ? $_POST['detalles'] : '';

    // Verificar si ya existe el producto en el carrito
    if (isset($_SESSION['carrito'][$id_producto])) {
        // Si ya existe, actualizar la cantidad y los detalles
        $_SESSION['carrito'][$id_producto]['cantidad'] += $cantidad;
        $_SESSION['carrito'][$id_producto]['detalles'] = $detalles;
    } else {
        // Si no existe, agregar el producto con la cantidad y detalles
        $_SESSION['carrito'][$id_producto] = [
            'cantidad' => $cantidad,
            'detalles' => $detalles
        ];
    }
}

// Eliminar producto del carrito
if (isset($_GET['action']) && $_GET['action'] == "remove") {
    $id_producto = $_GET['id'];
    if (isset($_SESSION['carrito'][$id_producto])) {
        unset($_SESSION['carrito'][$id_producto]);
    }
}

// Obtener productos en el carrito
$productos_en_carrito = [];
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    $ids = implode(',', array_keys($_SESSION['carrito']));

    if (!empty($ids)) {
        $consulta = "SELECT * FROM PRODUCTO WHERE id_producto IN ($ids)";
        $resultado = $conn->query($consulta);

        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                // Recuperar los detalles y cantidad almacenados en la sesión
                $fila['cantidad'] = $_SESSION['carrito'][$fila['id_producto']]['cantidad'];
                // Verificar si existe 'detalles' antes de acceder a él
                $fila['detalles'] = isset($_SESSION['carrito'][$fila['id_producto']]['detalles']) ? $_SESSION['carrito'][$fila['id_producto']]['detalles'] : 'N/A';
                $productos_en_carrito[] = $fila;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Producto - ArtesaníaBolivia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Incluye los scripts de Bootstrap -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Script de Google para Sign-In -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    
    <link rel="stylesheet" href="css/style_web.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md fixed w-full z-10">
        <nav class="container mx-auto px-6 py-1 flex justify-between items-center">
            <div class="flex items-center">
                <img src="img/logo_l.png" alt="Logo ArtesaníaBolivia" class="h-20 w-20 mr-3">
                <span class="font-bold text-xl" style="color: #e65b50;">ArtesaníaBoliviana</span>
            </div>
            <div class="flex items-center">
                <a href="index.php" class="nav-link text-gray-800 mx-3" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Inicio</a>
                <a href="productos-artesania-bolivia.php" class="nav-link text-gray-800 mx-3" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Productos</a>
                <a href="carrito.php" class="nav-link text-gray-800 mx-3" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Carrito</a>
                <a href="#" class="nav-link text-gray-800 mx-3" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Sobre Nosotros</a>
                
                <!-- Botones para abrir los modales -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Iniciar Sesión</button>
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#registroModal">Registrarse</button>


            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-24" style="padding-top: 150px;">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-12"><?php echo $producto['nombre']; ?></h1>

        <div class="flex flex-wrap">
            <!-- Galería de imágenes del producto -->
            <div class="w-full md:w-1/2 mb-6 md:mb-0">
                <div id="carousel-producto" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($imagenes as $index => $imagen): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="<?php echo 'Dashboard/Funciones_db/Crud_administrador/uploads/' . $imagen; ?>" alt="<?php echo $producto['nombre']; ?>" class="d-block w-100 h-96 object-cover">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Controles de la galería -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel-producto" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carousel-producto" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            </div>

            <!-- Detalles del producto -->
            <div class="w-full md:w-1/2 px-6">
                <h2 class="text-3xl font-bold mb-4">Detalles del producto</h2>
                <p class="text-gray-600 mb-4"><?php echo $producto['caracteristica']; ?></p>
                <div class="text-gray-500 mb-2">Artesano: <strong><?php echo $producto['nombre_comunario'] . ' ' . $producto['apellido_comunario']; ?></strong></div>
                <div class="text-gray-500 mb-4">Comunidad: <strong><?php echo $producto['nombre_comunidad']; ?></strong></div>
                <div class="text-gray-500 mb-4">Almacen: <strong><?php echo $producto['nombre_almacen']; ?></strong></div>
                <div class="text-gray-700 text-xl mb-4">Precio: <strong>BOB <?php echo number_format($producto['precio'], 2); ?></strong></div>
                <div class="text-gray-500 mb-4">Stock disponible: <strong><?php echo $producto['stock']; ?></strong> unidades</div>

                <!-- Campo para agregar detalles personalizados -->
                <form method="POST" action="carrito.php">
                    <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">

                    <div class="mb-4">
                        <label for="detalles" class="block text-gray-700 font-semibold">Detalles del producto (opcional):</label>
                        <input type="text" id="detalles" name="detalles" placeholder="Ej: Color, Talla, Personalización" class="w-full border border-gray-300 rounded-md px-4 py-2">
                    </div>

                    <div class="mb-4">
                        <label for="cantidad" class="block text-gray-700 font-semibold">Cantidad:</label>
                        <input type="number" id="cantidad" name="cantidad" value="1" min="1" class="w-full border border-gray-300 rounded-md px-4 py-2">
                    </div>

                    <button type="submit" name="agregar_carrito" class="w-full bg-gray-500 text-white px-4 py-2 rounded-md">Añadir al Carrito</button>
                </form>
            </div>
        </div>
    </main>

    <!-- Modal para Iniciar Sesión -->
    <div class="modal fade" id="loginModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Iniciar Sesión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="login.php" method="POST">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                        
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        
                        <button type="submit" class="btn btn-primary mt-3">Iniciar Sesión</button>
                    </form>
                    <a href="#">¿Olvidaste tu contraseña?</a>
                    <p class="mt-3">¿No tienes una cuenta? <a href="#" data-bs-toggle="modal" data-bs-target="#registroModal" data-bs-dismiss="modal">Registrate</a></p>
                    <!-- Botón de Google Sign-In -->
                    <div id="g_id_onload"
                        data-client_id="TU_CLIENTE_ID.apps.googleusercontent.com"
                        data-login_uri="http://localhost/login.php"
                        data-auto_select="true"
                        data-itp_support="true">
                    </div>
                    <div class="g_id_signin" data-type="standard"></div>

                    <footer>&copy; 2024 Plataforma Artesanal</footer>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Registro -->
    <div class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registroModalLabel">Registro de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="registro.php" method="POST">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                        
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                        
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        
                        <label for="password_confirm">Confirmar Contraseña</label>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-control" required>
                        
                        <button type="submit" class="btn btn-primary mt-3">Registrarse</button>
                    </form>
                    <p class="mt-3">¿Ya tienes una cuenta? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Inicia sesión</a></p>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="flex flex-wrap justify-between">
                <div class="w-full md:w-1/4 mb-6 md:mb-0">
                    <h3 class="text-lg font-semibold mb-4">ArtesaníaBolivia</h3>
                    <p class="text-gray-400">Conectando tradición y modernidad a través del arte.</p>
                </div>
                <div class="w-full md:w-1/4 mb-6 md:mb-0">
                    <h3 class="text-lg font-semibold mb-4">Enlaces Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-white transition duration-300">Inicio</a></li>
                        <li><a href="productos.html" class="text-gray-400 hover:text-white transition duration-300">Productos</a></li>
                        <li><a href="artesanos.html" class="text-gray-400 hover:text-white transition duration-300">Artesanos</a></li>
                        <li><a href="sobre-nosotros.html" class="text-gray-400 hover:text-white transition duration-300">Sobre Nosotros</a></li>
                    </ul>
                </div>
                <div class="w-full md:w-1/4 mb-6 md:mb-0">
                    <h3 class="text-lg font-semibold mb-4">Contacto</h3>
                    <p class="text-gray-400 mb-2">Email: info@artesaniabolivia.com</p>
                    <p class="text-gray-400">Teléfono: +591 2 1234567</p>
                </div>
                <div class="w-full md:w-1/4">
                    <h3 class="text-lg font-semibold mb-4">Síguenos</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">Facebook</a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">Instagram</a>
                        <a href="#" class="text-gray-400 hover:text-white transition duration-300">Twitter</a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-400">&copy; 2024 ArtesaníaBolivia. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        function handleCredentialResponse(response) {
            const data = { id_token: response.credential };

            // Enviar el token a tu servidor
            fetch('google_login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            }).then(response => {
                if (response.ok) {
                    window.location.href = 'dashboard.php'; // Redirigir si el login es exitoso
                }
            });
        }
        window.onload = function () {
            google.accounts.id.initialize({
                client_id: 'TU_CLIENTE_ID.apps.googleusercontent.com',
                callback: handleCredentialResponse
            });
            google.accounts.id.prompt(); // Mostrar el prompt de Google
        };

    </script>
</body>
</html>
