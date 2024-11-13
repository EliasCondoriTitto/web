<?php
// Iniciar la sesión para manejar el carrito
session_start();

// Conectar a la base de datos
$conn = new mysqli("sql103.infinityfree.com", "if0_37706422", "esx9Vdd8an", "if0_37706422_artesaniasbolivia");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener categorías de la tabla CATEGORIA
$sql_categorias = "SELECT id_categoria, nombre_categoria FROM CATEGORIA";
$result_categorias = $conn->query($sql_categorias);

// Obtener el id de la categoría seleccionada si existe
$id_categoria = isset($_GET['id_categoria']) ? $_GET['id_categoria'] : null;

// Consulta de productos con unión para obtener el nombre del comunario y la comunidad
$sql = "SELECT P.id_producto, P.nombre, P.caracteristica, P.id_categoria, P.precio, P.stock, P.imagenes, U.nombre AS nombre_comunario, U.apellido AS apellido_comunario, C.nombre AS nombre_comunidad 
        FROM PRODUCTO P
        INNER JOIN COMUNARIO CO ON P.id_comunario = CO.id_comunario
        INNER JOIN USUARIO U ON CO.id_comunario = U.id_usuario
        INNER JOIN COMUNIDAD C ON CO.id_comunidad = C.id_comunidad";

if ($id_categoria) {
    $sql .= " WHERE id_categoria = $id_categoria";
}
$result_productos = $conn->query($sql);



// Lógica para agregar productos al carrito
if (isset($_POST['agregar_carrito'])) {
    $id_producto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];

    // Verificar si ya existe el carrito en la sesión
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = array();
    }

    // Si el producto ya está en el carrito, actualizar la cantidad
    if (isset($_SESSION['carrito'][$id_producto])) {
        $_SESSION['carrito'][$id_producto]['cantidad'] += $cantidad;
    } else {
        // Si el producto no está en el carrito, obtener la información del producto
        $sql_producto = "SELECT id_producto, nombre, precio FROM PRODUCTO WHERE id_producto = $id_producto";
        $result_producto = $conn->query($sql_producto);
        if ($result_producto->num_rows > 0) {
            $producto = $result_producto->fetch_assoc();
            $_SESSION['carrito'][$id_producto] = array(
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'cantidad' => $cantidad
            );
        }
    }
}

// Verificar si el usuario está logueado
function usuarioLogueado() {
    return isset($_SESSION['id_usuario']);
}

// Obtener información del usuario si está logueado
$usuario_nombre = '';
$usuario_tipo = '';
if (usuarioLogueado()) {
    $id_usuario = $_SESSION['id_usuario'];
    $query = "SELECT nombre, rol FROM usuario WHERE id_usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $usuario_nombre = $row['nombre'];
        $usuario_tipo = $row['rol'];
    }
    $stmt->close();
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - ArtesaníaBolivia</title>
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
<body class="font-sans bg-gray-100">
    <header class="bg-white shadow-md fixed w-full z-10">
        <nav class="container mx-auto px-6 py-1 flex justify-between items-center">
            <div class="flex items-center">
                <img src="img/logo_l.png" alt="Logo ArtesaníaBolivia" class="h-20 w-20 mr-3">
                <span class="font-bold text-xl" style="color: #e65b50;">ArtesaníaBoliviana</span>
            </div>
            <div class="hidden md:flex items-center">
                <a href="index.php" class="nav-link mx-3" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Inicio</a>
                <a href="productos-artesania-bolivia.php" class="nav-link mx-3" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Productos</a>
                <a href="carrito.php" class="nav-link mx-3" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Carrito</a>
                <a href="#" class="nav-link mx-3" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Sobre Nosotros</a>
                
                <?php if (usuarioLogueado()): ?>
                <div class="relative">
                    <button id="userMenuButton" class="flex items-center focus:outline-none" onclick="toggleUserMenu()">
                        <span class="mr-2"><?php echo htmlspecialchars($usuario_nombre); ?></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="userMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden">
                        <?php
                            $dashboard_url = 'dashboard.php'; // URL por defecto
                            switch($usuario_tipo) {
                                case 'comprador':
                                    $dashboard_url = 'Dashboard/Dashboard_comprador.php';
                                    break;
                                case 'vendedor':
                                    $dashboard_url = 'Dashboard/Dashboard_comunario.php';
                                    break;
                                case 'delivery':
                                    $dashboard_url = 'Dashboard/Dashboard_delivery.php';
                                    break;
                                case 'administrador':
                                    $dashboard_url = 'Dashboard/Dashboard_administrador.php';
                                    break;
                            }
                        ?>
                        <a href="<?php echo $dashboard_url; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                        <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cerrar Sesión</a>
                    </div>
                </div>
                <?php else: ?>
                <button type="button" class="btn btn-primary mx-2" data-bs-toggle="modal" data-bs-target="#loginModal">Iniciar Sesión</button>
                <button type="button" class="btn btn-secondary mx-2" data-bs-toggle="modal" data-bs-target="#registroModal">Registrarse</button>
                <?php endif; ?>
            </div>
            <div class="md:hidden flex items-center">
                <button id="mobileMenuButton" class="focus:outline-none" onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
            </div>
        </nav>
        <div id="mobileMenu" class="hidden md:hidden bg-white shadow-md">
            <a href="index.php" class="block px-4 py-2" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Inicio</a>
            <a href="productos-artesania-bolivia.php" class="block px-4 py-2" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Productos</a>
            <a href="carrito.php" class="block px-4 py-2" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Carrito</a>
            <a href="#" class="block px-4 py-2" style="color: black;" onmouseover="this.style.color='#e65b50'" onmouseout="this.style.color='black'">Sobre Nosotros</a>

            <?php if (!usuarioLogueado()): ?>
            <button type="button" class="btn btn-primary block w-full text-left" data-bs-toggle="modal" data-bs-target="#loginModal">Iniciar Sesión</button>
            <button type="button" class="btn btn-secondary block w-full text-left" data-bs-toggle="modal" data-bs-target="#registroModal">Registrarse</button>
            <?php else: ?>
            <a href="<?php echo $dashboard_url; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cerrar Sesión</a>
            <?php endif; ?>
        </div>
    </header>

    <script>
    function toggleMobileMenu() {
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.classList.toggle('hidden');
    }

    function toggleUserMenu() {
        const userMenu = document.getElementById('userMenu');
        userMenu.classList.toggle('hidden');
    }
    </script>

    <main class="container mx-auto px-6 py-24" style="padding-top: 150px;">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Nuestros Productos Artesanales</h1>
        
        <!-- Mostrar los filtros de categorías -->
        <div class="flex justify-center mb-8">
            <div class="inline-flex rounded-md shadow-sm" role="group">
            <a href="productos-artesania-bolivia.php" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-custom-200 hover:bg-gray-100 hover:text-custom-700 focus:z-10 focus:ring-2 focus:ring-indigo-700 focus:text-indigo-700 no-underline">Todos</a>       
                <?php while($cat = $result_categorias->fetch_assoc()): ?>
                    <a href="productos-artesania-bolivia.php?id_categoria=<?php echo $cat['id_categoria']; ?>" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-indigo-700 focus:z-10 focus:ring-2 focus:ring-indigo-700 focus:text-indigo-700 no-underline">
                        <?php echo $cat['nombre_categoria']; ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Mostrar productos -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php while($row = $result_productos->fetch_assoc()): ?>
            <div class="product-card bg-white rounded-lg shadow-md overflow-hidden">
            <?php
                // Deserializar las imágenes
                $imagenes = unserialize($row['imagenes']);
                if ($imagenes && is_array($imagenes) && !empty($imagenes)) {
                    // Mostrar la primera imagen del array
                    $imagen_principal = $imagenes[0];
                } else {
                    // Si no hay imágenes, mostrar una imagen por defecto
                    $imagen_principal = 'img/404.png';
                }
            ?>
            <div id="carousel-<?php echo $row['id_producto']; ?>" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($imagenes as $index => $imagen): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="<?php echo 'Dashboard/Funciones_db/Crud_administrador/uploads/' . $imagen; ?>" alt="<?php echo $row['nombre']; ?>" class="d-block w-100 h-48 object-cover">
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- Controles para la galería -->
                <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $row['id_producto']; ?>" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $row['id_producto']; ?>" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
                </div>
                
                <div class="p-6">
                    <h3 class="font-semibold text-xl mb-2"><?php echo $row['nombre']; ?></h3>
                    <p class="text-gray-600 mb-4"><?php echo $row['caracteristica']; ?></p>
                    <div class="text-gray-500 mb-4">Artesano: <strong><?php echo $row['nombre_comunario'] . ' ' . $row['apellido_comunario']; ?></strong></div>
                    <div class="text-gray-500 mb-4">Comunidad: <strong><?php echo $row['nombre_comunidad']; ?></strong></div>
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-bold text-custom-800">BOB <?php echo $row['precio']; ?></span>
                        <span class="text-sm text-gray-600">Stock: <?php echo $row['stock']; ?></span>
                    </div>

                    <!-- Botón para abrir el modal -->
                    <!-- <button type="button" class="w-full text-white px-4 py-2 rounded-md block text-center mt-4" data-bs-toggle="modal" data-bs-target="#modal-<?php echo $row['id_producto']; ?>" style="background-color: #f69060;">Ver Detalles</button> -->
                    <a href="detalles_producto.php?id_producto=<?php echo $row['id_producto']; ?>" class="w-full text-white px-4 py-2 rounded-md block text-center mt-4 no-underline" style="background-color: #FFA07A;">Ver Detalles</a>


                    <form method="POST" action="productos-artesania-bolivia.php">
                        <input type="hidden" name="id_producto" value="<?php echo $row['id_producto']; ?>">
                        <input type="number" name="cantidad" value="1" min="1" class="w-full border border-gray-300 rounded-md mt-2 mb-2 px-2 py-1">
                        <button type="submit" name="agregar_carrito" class="w-full bg-gray-500 text-white px-4 py-2 rounded-md">Añadir al Carrito</button>
                    </form>

                </div>
            </div>
            <?php endwhile; ?>
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

    <!-- Modal de detalles producto -->
    <div class="modal fade" id="modal-<?php echo $row['id_producto']; ?>" tabindex="-1" aria-labelledby="modalLabel-<?php echo $row['id_producto']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel-<?php echo $row['id_producto']; ?>"><?php echo $row['nombre']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <!-- Mostrar galería de imágenes -->
                        <div id="carousel-<?php echo $row['id_producto']; ?>" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php foreach ($imagenes as $index => $imagen): ?>
                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                        <img src="<?php echo 'Dashboard/Funciones_db/Crud_administrador/uploads/' . $imagen; ?>" alt="<?php echo $row['nombre']; ?>" class="d-block w-100">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $row['id_producto']; ?>" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $row['id_producto']; ?>" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h4>Características del producto</h4>
                        <p><?php echo $row['caracteristica']; ?></p>

                        <h4>Información del Artesano</h4>
                        <p>Artesano: <strong><?php echo $row['nombre_comunario'] . ' ' . $row['apellido_comunario']; ?></strong></p>
                        <p>Comunidad: <strong><?php echo $row['nombre_comunidad']; ?></strong></p>

                        <div class="mt-4">
                            <h4>Precio y Stock</h4>
                            <p>Precio: BOB <?php echo $row['precio']; ?></p>
                            <p>Stock: <?php echo $row['stock']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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

        function toggleUserMenu() {
        var menu = document.getElementById('userMenu');
        menu.classList.toggle('hidden');
    }

    // Cerrar el menú si se hace clic fuera de él
    window.onclick = function(event) {
        if (!event.target.matches('#userMenuButton') && !event.target.closest('#userMenu')) {
            var menu = document.getElementById('userMenu');
            if (!menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
            }
        }
    }

    </script>

</body>
</html>

<?php
$conn->close();
?>
