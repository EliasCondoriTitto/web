<?php
// Datos de conexión a la base de datos
$servername = "localhost";
$username = "root";  // Cambiar según tu configuración de MySQL
$password = "";      // Cambiar según tu configuración de MySQL
$database = "bdproduc_artesanales"; // Cambia este valor al nombre de tu base de datos

// Crear la conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);

// Verificar si la conexión ha fallado
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// **Función para mostrar usuarios en el dashboard del administrador**
function mostrarUsuarios() {
    global $conn;  // Utilizamos la variable de conexión global

    // Consulta para obtener todos los usuarios
    $sql = "SELECT * FROM USUARIO";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Usuarios</h2>";
        echo "<table>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Estado</th>
                </tr>";
        // Imprimir los datos de cada usuario en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["nombre"]." ".$row["apellido"]."</td>
                    <td>".$row["correo"]."</td>
                    <td>".$row["estado"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay usuarios registrados.</p>";
    }
}

//** Funcion para mostrar almacenes en el dashboard del administrador */
function mostrarAlmacen() {
    global $conn;

    // Consulta para obtener todas los almacenes 
    $sql = "SELECT * FROM ALMACEN";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Almacén</h2>";
        echo "<table>
                <tr>
                    <th>Nombre Almacén</th>
                    <th>Fecha registro</th>
                </tr>";
        // Imprimir los datos de cada almacen en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["nombre"]."</td>
                    <td>".$row["fecha_registro"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay almacenes registrados.</p>";
    }
}


//** Funcion para mostrar departamentos en el dashboard del administrador */
function mostrarDepartamentos() {
    global $conn;

    // Consulta para obtener todas las comunidades
    $sql = "SELECT * FROM DEPARTAMENTO";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Departamentos</h2>";
        echo "<table>
                <tr>
                    <th>Nombre Departamento</th>
                    <th>Capital</th>
                    <th>Superficie</th>
                </tr>";
        // Imprimir los datos de cada departamento en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["nombre_departamento"]."</td>
                    <td>".$row["capital"]."</td>
                    <td>".$row["superficie"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay comunidades registradas.</p>";
    }
}


// **Función para mostrar comunidades en el dashboard del administrador**
function mostrarComunidades() {
    global $conn;

    // Consulta para obtener todas las comunidades
    $sql = "SELECT * FROM COMUNIDAD";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Comunidades</h2>";
        echo "<table>
                <tr>
                    <th>Nombre Comunidad</th>
                    <th>Departamento</th>
                    <th>Número de Habitantes</th>
                </tr>";
        // Imprimir los datos de cada comunidad en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["nombre"]."</td>
                    <td>".$row["nombre_departamento"]."</td>
                    <td>".$row["nro_habitantes"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay comunidades registradas.</p>";
    }
}



// **Función para mostrar empresas en el dashboard del administrador**
function mostrarEmpresaD() {
    global $conn;

    // Consulta para obtener todos los empresas
    $sql = "SELECT * FROM EMPRESA";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Empresas</h2>";
        echo "<table>
                <tr>
                    <th>Nombre Empresa</th>
                    <th>Dirección</th>
                </tr>";
        // Imprimir los datos de cada producto en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["nombre"]."</td>
                    <td>".$row["direccion"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay empresas registrados.</p>";
    }
}



// **Función para mostrar categorias en el dashboard del administrador**
function mostrarCategoriaP() {
    global $conn;

    // Consulta para obtener todos las categorias
    $sql = "SELECT * FROM CATEGORIA";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Categorías</h2>";
        echo "<table>
                <tr>
                    <th>Nombre Categoría</th>
                    <th>Descripción</th>

                </tr>";
        // Imprimir los datos de cada categoria en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["nombre_categoria"]."</td>
                    <td>".$row["descripcion"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay categorias registrados.</p>";
    }
}


// **Función para mostrar productos en el dashboard del administrador**
function mostrarProductos() {
    global $conn;

    // Consulta para obtener todos los productos
    $sql = "SELECT * FROM PRODUCTO";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Productos</h2>";
        echo "<table>
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Descripcion</th>
                    <th>Stock</th>
                </tr>";
        // Imprimir los datos de cada producto en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["nombre"]."</td>
                    <td>".$row["precio"]."</td>
                    <td>".$row["caracteristica"]."</td>
                    <td>".$row["stock"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay productos registrados.</p>";
    }
}


// **Función para mostrar reportes en el dashboard del administrador**
function mostrarReportes() {
    // Aquí puedes agregar lógica para generar reportes como ventas totales, pedidos completados, etc.
    echo "<h2>Reportes de Ventas</h2>";
    echo "<p>Esta sección estará dedicada a la generación de reportes de ventas y análisis de datos.</p>";
}

// **Función para mostrar pedidos en el dashboard del administrador**
function mostrarPedidos() {
    global $conn;

    // Consulta para obtener todos los pedidos
    $sql = "SELECT * FROM PEDIDO_CARRITO";
    $result = $conn->query($sql);

    // Comprobar si se obtuvieron resultados
    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Pedidos</h2>";
        echo "<table>
                <tr>
                    <th>ID Pedido_Carrito</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>";
        // Imprimir los datos de cada pedido en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["id_pedido_carrito"]."</td>
                    <td>".$row["cantidad"]."</td>
                    <td>".$row["estado_pedido"]."</td>
                    <td>".$row["fecha_pedido"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay pedidos registrados.</p>";
    }
}
?>
