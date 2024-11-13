<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>


<?php
session_start();
require 'db.php'; // Conexión a la base de datos

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

// Obtener almacenes
$query_almacenes = "SELECT * FROM almacen";
$result_almacenes = $conn->query($query_almacenes);
$almacenes = [];
while ($row = $result_almacenes->fetch_assoc()) {
    $almacenes[] = $row;
}


// Obtener empresas de delivery
$query_delivery = "SELECT * FROM empresa";
$result_delivery = $conn->query($query_delivery);
$empresas_delivery = [];
while ($row = $result_delivery->fetch_assoc()) {
    $empresas_delivery[] = $row;
}

$query_almacen = "SELECT a.id_almacen, 
                           a.nombre, 
                           u.latitud, 
                           u.longitud,
                           u.departamento,
                           u.provincia,
                           u.calle,
                           u.zona,
                           u.nro_puerta
                    FROM almacen a 
                    INNER JOIN ubicacion u ON a.id_almacen = u.id_almacen";

$result_almacen = mysqli_query($conn, $query_almacen);
$almacen = [];

while ($row = mysqli_fetch_assoc($result_almacen)) {
    $almacen[] = $row;
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - ArtesaníaBolivia</title>
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
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Tu Carrito</h1>

        <?php if (empty($productos_en_carrito)): ?>
            <p class="text-center text-gray-600">No tienes productos en el carrito.</p>
        <?php else: ?>
            <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
                <table class="w-full table-auto">
                <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Producto</th>
                            <th class="py-3 px-6 text-left">Cantidad</th>
                            <th class="py-3 px-6 text-left">Detalles</th> <!-- Mostrar los detalles -->
                            <th class="py-3 px-6 text-left">Precio</th>
                            <th class="py-3 px-6 text-left">Subtotal</th>
                            <th class="py-3 px-6 text-left">Stock Disponible</th>
                            <th class="py-3 px-6 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <?php $total = 0; ?>
                        <?php foreach ($productos_en_carrito as $producto): ?>
                            <?php
                            $precio = (float) $producto['precio'];
                            $cantidad = (int) $producto['cantidad'];
                            $stock = (int) $producto['stock'];
                            $detalles = $producto['detalles'];

                            $subtotal = $precio * $cantidad;
                            $total += $subtotal;
                            ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    <div class="flex items-center">
                                        
                                        <span><?php echo $producto['nombre']; ?></span>
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-left"><?php echo $cantidad; ?></td>
                                <td class="py-3 px-6 text-left"><?php echo $detalles ? $detalles : 'N/A'; ?></td> <!-- Mostrar los detalles -->
                                <td class="py-3 px-6 text-left"><?php echo "BOB " . number_format($precio, 2); ?></td>
                                <td class="py-3 px-6 text-left"><?php echo "BOB " . number_format($subtotal, 2); ?></td>
                                <td class="py-3 px-6 text-left"><?php echo $stock - $cantidad; ?> unidades</td>
                                <td class="py-3 px-6 text-center">
                                    <a href="carrito.php?action=remove&id=<?php echo $producto['id_producto']; ?>" class="text-red-500 hover:text-red-700">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-xl font-bold">Total: BOB <?php echo $total; ?></span>
                <?php if (usuarioLogueado()): ?>
                    <a href="#" class="btn-primary text-white px-4 py-2 rounded-md" data-bs-toggle="modal" data-bs-target="#checkoutModal">Finalizar Compra</a>
                <?php else: ?>
                    <a href="#" onclick="mostrarModalLogin()" class="btn-primary text-white px-4 py-2 rounded-md">Finalizar Compra</a>
                <?php endif; ?>
                <a href="vaciar_carrito.php" class="btn-primary text-white px-4 py-2 rounded-md">Vaciar carrito</a>
            </div>
            <!-- Botón "Seguir Comprando" debajo de "Finalizar Compra" -->
            <div class="flex justify-center items-center ">
                <a href="productos-artesania-bolivia.php" class="btn-three text-black px-4 py-2 rounded-md">Seguir Comprando</a>
            </div>
            

        <?php endif; ?>
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

    <!-- Modal para Checkout -->
    <div class="modal fade" id="checkoutModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true"> 
        <style>
            #checkoutModal .modal-dialog {
                max-width: 80%;
                width: 80%;
                margin: 1.75rem auto;
            }
            #checkoutModal .modal-content {
                width: 100%;
                max-width: none;
                padding: 30px;
            }
            #checkoutModal .modal-body {
                padding: 20px;
            }
            #checkoutModal .row {
                margin-right: 0;
                margin-left: 0;
            }
            #deliverySection {
                display: none;
            }
            #mapContainer {
                height: 300px;
                width: 100%;
                margin-bottom: 20px;
                margin-top: 10px;
            }
            @media (max-width: 768px) {
                #checkoutModal .modal-dialog {
                    max-width: 95%;
                    width: 95%;
                    margin: 1rem auto;
                }
                #checkoutModal .modal-content {
                    padding: 15px;
                }
                #checkoutModal .modal-body {
                    padding: 10px;
                }
            }
        </style>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutModalLabel">Finalizar Compra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        <!-- Primera Sección: Resumen de compra y métodos de pago -->
                        <div id="productsSection">
                            <div class="row">
                                <div class="col-md-8">
                                    <h2 class="mb-4">Detalles de la Compra</h2>
                                    <table class="w-full table-auto">
                                        <thead>
                                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Detalles</th>
                                                <th>Precio</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($productos_en_carrito as $producto): ?>
                                                <tr>
                                                    <td><?php echo $producto['nombre']; ?></td>
                                                    <td><?php echo $producto['cantidad']; ?></td>
                                                    <td><?php echo $producto['detalles']; ?></td>
                                                    <td>BOB <?php echo number_format($producto['precio'], 2); ?></td>
                                                    <td>BOB <?php echo number_format($producto['precio'] * $producto['cantidad'], 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <hr>
                                    <h3 class="mb-4">Subtotal Productos: BOB <?php echo $total; ?></h3>
                                </div>
                                <div class="col-md-4">
                                    <h2 class="mb-4">Métodos de Pago</h2>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethod1" value="creditCard" required>
                                        <label class="form-check-label" for="paymentMethod1">Tarjeta de Crédito/Débito</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethod2" value="bankTransfer" required>
                                        <label class="form-check-label" for="paymentMethod2">Transferencia Bancaria</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethod3" value="mobilePayment" required>
                                        <label class="form-check-label" for="paymentMethod3">Pago Móvil (QR)</label>
                                    </div>
                                    
                                    <button type="button" class="btn btn-primary" id="proceedToPayment">Proceder al Pago</button>
                                </div>
                            </div>
                        </div>

                        <!-- Segunda Sección: Información de entrega -->
                        <div id="deliverySection" style="display: none;">
                            <h2 class="mb-4">Información de Entrega y Detalles de Pago</h2>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="warehouseSelect" class="form-label">Seleccionar Almacén</label>
                                        <select class="form-select" id="warehouseSelect" name="almacen" required>
                                            <option value="">Seleccione un almacén</option>
                                            <?php foreach ($almacenes as $almacen): ?>
                                                <?php
                                                    // Validar que las claves existen en el arreglo $almacen antes de acceder a ellas
                                                    $latitud = isset($almacen['latitud']) ? $almacen['latitud'] : '';
                                                    $longitud = isset($almacen['longitud']) ? $almacen['longitud'] : '';
                                                    $departamento = isset($almacen['departamento']) ? $almacen['departamento'] : '';
                                                    $provincia = isset($almacen['provincia']) ? $almacen['provincia'] : '';
                                                    $calle = isset($almacen['calle']) ? $almacen['calle'] : '';
                                                    $nro_puerta = isset($almacen['nro_puerta']) ? $almacen['nro_puerta'] : '';
                                                    $zona = isset($almacen['zona']) ? $almacen['zona'] : '';
                                                    $nombre = isset($almacen['nombre']) ? $almacen['nombre'] : '';
                                                ?>
                                                <option value="<?php echo $almacen['id_almacen']; ?>"
                                                        data-lat="<?php echo $latitud; ?>"
                                                        data-lng="<?php echo $longitud; ?>"
                                                        data-departamento="<?php echo htmlspecialchars($departamento); ?>"
                                                        data-provincia="<?php echo htmlspecialchars($provincia); ?>"
                                                        data-direccion="<?php echo htmlspecialchars($calle . ' ' . $nro_puerta . ', ' . $zona); ?>">
                                                    <?php echo htmlspecialchars($departamento . ' ' . $provincia); ?>
                                                    <?php echo htmlspecialchars($nombre); ?> 
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div id="warehouseInfo" class="mb-3" style="display: none;">
                                        <h6>Información del Almacén</h6>
                                        <p><strong>Departamento:</strong> <span id="warehouseDepartamento"></span></p>
                                        <p><strong>Provincia:</strong> <span id="warehouseProvincia"></span></p>
                                        <p><strong>Dirección:</strong> <span id="warehouseDireccion"></span></p>
                                    </div>

                                    <div class="mb-3">
                                        <label for="nombre_comprador" class="form-label">Nombre del Comprador</label>
                                        <input type="text" class="form-control" id="nombre_comprador" name="nombre_comprador" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Dirección de Entrega</label>
                                        <input type="text" class="form-control" id="address" name="direccion_entrega" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reference" class="form-label">Referencia</label>
                                        <input type="text" class="form-control" id="reference" name="referencia">
                                    </div>

                                    <div id="mapContainer" class="mt-3"></div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="latitude" class="form-label">Latitud</label>
                                                <input type="text" class="form-control" id="latitude" name="latitud" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="longitude" class="form-label">Longitud</label>
                                                <input type="text" class="form-control" id="longitude" name="longitud" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="deliveryCompany" class="form-label">Empresa de Delivery</label>
                                        <select class="form-select" id="deliveryCompany" name="empresa_delivery" required>
                                            <option value="">Seleccione una empresa</option>
                                            <?php foreach ($empresas_delivery as $empresa): ?>
                                                <option value="<?php echo $empresa['id_empresa']; ?>"><?php echo $empresa['nombre']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Resumen de Costos -->
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <h3>Resumen de Costos</h3>
                                            <table class="table w-100">
                                                <tr>
                                                    <td>Subtotal Productos:</td>
                                                    <td>BOB <span id="subtotalProducts"><?php echo $total; ?></span></td>
                                                </tr>
                                                <tr>
                                                    <td>Costo de Envío:</td>
                                                    <td>BOB <span id="deliveryCost">0.00</span></td>
                                                </tr>
                                                <tr class="table-primary">
                                                    <td><strong>Total General:</strong></td>
                                                    <td><strong>BOB <span id="totalGeneral"><?php echo $total; ?></span></strong></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Columna de detalles del método de pago seleccionado -->
                                <div class="col-md-6">
                                    <h3>Detalles de Pago</h3>
                                    <div id="paymentDetailsContainer" class="mt-4">
                                        <div id="creditCardDetails" class="payment-details" style="display: none;">
                                            <h4>Tarjeta de Crédito/Débito</h4>
                                            <input type="text" class="form-control mb-3" name="numero_tarjeta" placeholder="Número de Tarjeta" required>
                                            <input type="text" class="form-control mb-3" name="nombre_tarjeta" placeholder="Nombre en la Tarjeta" required>
                                            <input type="text" class="form-control mb-3" name="fecha_expiracion" placeholder="Fecha de Expiración" required>
                                            <input type="text" class="form-control mb-3" name="cvv" placeholder="CVV" required>
                                        </div>
                                        <div id="bankTransferDetails" class="payment-details" style="display: none;">
                                            <h4>Transferencia Bancaria</h4>
                                            <p>Realice la transferencia a la cuenta bancaria que le será proporcionada tras completar la compra.</p>
                                        </div>
                                        <div id="qrCodeContainer" class="payment-details" style="display: none;">
                                            <h4>Pago Móvil (QR)</h4>
                                            <p>Escanee el código QR proporcionado para completar el pago móvil.</p>
                                            <div id="qrCode"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <form action="generar_pdf.php" method="POST">
                                    <button type="submit" class="btn btn-primary" id="confirmOrder">Confirmar Pedido</button>
                                    </form>
                                    <button type="button" class="btn btn-secondary me-2" id="backToProducts">Volver</button>
                                </div>
                            </div>

                        </div>
                    </form>
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
                        <li><a href="productos-artesania-bolivia.php" class="text-gray-400 hover:text-white transition duration-300">Productos</a></li>
                        <li><a href="artesanos-artesania-bolivia.php" class="text-gray-400 hover:text-white transition duration-300">Artesanos</a></li>
                        <li><a href="sobre-nosotros.php" class="text-gray-400 hover:text-white transition duration-300">Sobre Nosotros</a></li>
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

    <script src="https://maps.googleapis.com/maps/api/js?key=TU_API_KEY&callback=initMap" async defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-qrcode/1.0/jquery.qrcode.min.js"></script>
                        <!----------------  -------------------->
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

        function mostrarModalLogin() {
            $('#loginModal').modal('show');
        }

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

    document.addEventListener("DOMContentLoaded", function () {
        // Función para mostrar/ocultar secciones y detalles de métodos de pago
        const paymentMethodInputs = document.querySelectorAll("input[name='paymentMethod']");
        const productsSection = document.getElementById("productsSection");

        const deliverySection = document.getElementById("deliverySection");
        const proceedToPaymentButton = document.getElementById("proceedToPayment");
        
        // Inicializar los detalles de pago para ocultarlos
        const creditCardDetails = document.getElementById("creditCardDetails");
        const bankTransferDetails = document.getElementById("bankTransferDetails");
        const qrCodeContainer = document.getElementById("qrCodeContainer");

        // Muestra la Segunda Sección sin ocultar la Primera
        proceedToPaymentButton.addEventListener("click", function () {
            deliverySection.style.display = "block"; // Muestra la Segunda Sección
        });

        // Manejador para mostrar detalles de pago según el método seleccionado
        paymentMethodInputs.forEach((input) => {
            input.addEventListener("change", function () {
                document.querySelectorAll(".payment-details").forEach((el) => (el.style.display = "none"));
                if (this.value === "creditCard") {
                    document.getElementById("creditCardDetails").style.display = "block";
                } else if (this.value === "bankTransfer") {
                    document.getElementById("bankTransferDetails").style.display = "block";
                } else if (this.value === "mobilePayment") {
                    document.getElementById("qrCodeContainer").style.display = "block";
                    generateQRCode();
                }
            });
        });

        // Generación de QR para pago móvil
        function generateQRCode() {
            const qrCodeContainer = document.getElementById("qrCode");
            qrCodeContainer.innerHTML = '';
            new QRCode(qrCodeContainer, {
                text: 'https://ejemplo-pago-movil.com/pago123',
                width: 200,
                height: 200
            });
        }
    });



    document.addEventListener("DOMContentLoaded", function () {
        // Elementos del DOM
        const proceedToPaymentBtn = document.getElementById('proceedToPayment');

        const productsSection = document.getElementById('productsSection');
        const deliverySection = document.getElementById('deliverySection');
        
        const backToProductsBtn = document.getElementById('backToProducts');
        const paymentDetailsSection = document.getElementById("paymentDetailsSection");
        const paymentMethodInputs = document.querySelectorAll("input[name='paymentMethod']");
        const deliveryCompanySelect = document.getElementById('deliveryCompany');
        const warehouseSelect = document.getElementById('warehouseSelect');
        const qrCodeContainer = document.getElementById("qrCode");
        const mapContainer = document.getElementById('mapContainer');

        let map, marker, warehouseMarker, deliveryMarker;
        let currentWarehouse = null;
        const BASE_DELIVERY_COST = 2;
        const COST_PER_KM = 2;
        const WAREHOUSE_LOCATION = [-16.490824474689433, -68.12022231780529];

        // Inicialmente oculta todas las secciones de detalles de pago
        document.querySelectorAll(".payment-details").forEach(el => el.style.display = "none");

        // Manejador del botón de proceder al pago
        if (proceedToPaymentBtn) {
            proceedToPaymentBtn.addEventListener('click', function () {

                deliverySection.style.display = 'block';

                // Inicializa el mapa
                setTimeout(() => {
                    if (typeof initMap === 'function') {
                        initMap();
                    }
                }, 100);

                // Muestra sección de detalles de pago
                paymentDetailsSection.style.display = "flex";
            });
        }

        // Manejador de selección de empresa de delivery
        if (deliveryCompanySelect) {
            deliveryCompanySelect.addEventListener('change', function () {
                if (this.value && marker) {
                    calculateDeliveryCost(marker.getLatLng());
                }
            });
        }


        // Inicialización del mapa
        function initMap() {
            const mapContainer = document.getElementById('mapContainer');
            if (!mapContainer) return;

            map = L.map('mapContainer').setView(WAREHOUSE_LOCATION, 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Agrega el marcador del almacén
        warehouseMarker = L.marker(WAREHOUSE_LOCATION, {
            icon: L.divIcon({
                className: 'warehouse-marker',
                html: '📦',
                iconSize: [25, 25]
            })
        }).addTo(map).bindPopup('Almacén');

            marker = L.marker(WAREHOUSE_LOCATION, {
                draggable: true
            }).addTo(map);

            marker.on('dragend', function (event) {
                const position = marker.getLatLng();
                updateCoordinates(position);
                calculateDeliveryCost(position);
            });

            map.on('click', function (event) {
                marker.setLatLng(event.latlng);
                updateCoordinates(event.latlng);
                calculateDeliveryCost(event.latlng);
            });

            // Asegurarse de que el mapa se ajuste a su contenedor
            setTimeout(function() {
                map.invalidateSize();
            }, 100);
        }

        // Evento para manejar la selección de almacén
        
        
        
        // Cálculo del costo de entrega
        function calculateDeliveryCost(position) {
            const distance = L.latLng(WAREHOUSE_LOCATION).distanceTo([position.lat, position.lng]) / 1000;
            const deliveryCost = BASE_DELIVERY_COST + (COST_PER_KM * distance);
            const roundedDeliveryCost = Math.round(deliveryCost * 100) / 100;
            document.getElementById('deliveryCost').textContent = roundedDeliveryCost.toFixed(2);
            updateTotal();
        }

        // Actualizar total general
        function updateTotal() {
            const subtotal = parseFloat(document.getElementById('subtotalProducts').textContent);
            const deliveryCost = parseFloat(document.getElementById('deliveryCost').textContent);
            const total = subtotal + deliveryCost;
            document.getElementById('totalGeneral').textContent = total.toFixed(2);
        }



        // Actualizar coordenadas en los inputs
        function updateCoordinates(latlng) {
            document.getElementById('latitude').value = latlng.lat.toFixed(6);
            document.getElementById('longitude').value = latlng.lng.toFixed(6);
        }

    });

</script>
</body>
</html>