<?php
session_start();
require 'db.php'; // Conexión a la base de datos

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
    <title>ArtesaníaBoliviana - Plataforma de Comercio Artesanal</title>
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
    

    <main class="pt-16">
        <section class="hero-pattern py-20">
            <div class="container mx-auto px-6 text-center">
                <h1 class="text-5xl font-bold text-gray-800 mb-4">Descubre la Artesanía Boliviana</h1>
                <p class="text-xl text-gray-600 mb-8">Conectamos artesanos tradicionales con amantes del arte de todo el mundo</p>
                <a href="productos-artesania-bolivia.php" class="btn-primary text-white px-8 py-3 rounded-full text-lg shadow-lg no-underline">Explorar Productos</a>
            </div>
        </section>

        <section class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-semibold text-center text-gray-800 mb-12">Categorías Destacadas</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div class="category-card bg-gray-100 rounded-lg overflow-hidden shadow-md">
                        <img src="img/Productos/Aguayo.jpeg" alt="Textiles" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="font-semibold text-xl mb-2">Textiles</h3>
                            <p class="text-gray-600">Descubre hermosos tejidos y bordados tradicionales.</p>
                        </div>
                    </div>
                    <div class="category-card bg-gray-100 rounded-lg overflow-hidden shadow-md">
                        <img src="img/Productos/Tazas.jpeg" alt="Cerámica" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="font-semibold text-xl mb-2">Cerámica</h3>
                            <p class="text-gray-600">Explora piezas únicas hechas a mano por artesanos locales.</p>
                        </div>
                    </div>
                    <div class="category-card bg-gray-100 rounded-lg overflow-hidden shadow-md">
                        <img src="img/Productos/joyeria.jpg" alt="Joyería" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="font-semibold text-xl mb-2">Joyería</h3>
                            <p class="text-gray-600">Adórnate con joyas inspiradas en la cultura boliviana.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        

        
        <section><div class="fondomedio"></div></section>
        


        <section class="py-16 bg-indigo-100">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-semibold text-gray-800 mb-12">¿Por qué elegir ArtesaníaBolivia?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <div>
                        <img src="img/Autenticidad.jpeg" alt="Ícono de autenticidad" class="mx-auto mb-4 feature-icon">
                        <h3 class="font-semibold text-xl mb-2">Autenticidad Garantizada</h3>
                        <p class="text-gray-600">Cada pieza es única y hecha a mano por artesanos bolivianos.</p>
                    </div>
                    <div>
                        <img src="img/Apoyo.jpeg" alt="Ícono de apoyo" class="mx-auto mb-4 feature-icon">
                        <h3 class="font-semibold text-xl mb-2">Apoyo Directo</h3>
                        <p class="text-gray-600">Tu compra beneficia directamente a los artesanos y sus comunidades.</p>
                    </div>
                    <div>
                        <img src="img/Calidad.jpeg" alt="Ícono de calidad" class="mx-auto mb-4 feature-icon">
                        <h3 class="font-semibold text-xl mb-2">Calidad Superior</h3>
                        <p class="text-gray-600">Productos cuidadosamente seleccionados por su calidad y belleza.</p>
                    </div>
                </div>
            </div>
        </section>
        
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
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Inicio</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Productos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Artesanos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300">Sobre Nosotros</a></li>
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
