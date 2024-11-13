<?php
// Conexión a la base de datos
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

// **Función para mostrar los productos del comunario**
function mostrarProductosComunario() {
    global $conn;

    // Aquí puedes cambiar por el ID del comunario específico que esté logueado
    $id_comunario = 1; // Ejemplo: El comunario con ID 1

    // Consulta para obtener los productos del comunario
    $sql = "SELECT * FROM PRODUCTO WHERE id_comunario = $id_comunario";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Productos</h2>";
        echo "<table>
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>";
        // Imprimir los productos en una tabla
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["Nombre"]."</td>
                    <td>".$row["precio"]."</td>
                    <td>".$row["stock"]."</td>
                    <td>
                        <a href='editar_producto.php?id=".$row["IdProducto"]."'>Editar</a> |
                        <a href='eliminar_producto.php?id=".$row["IdProducto"]."'>Eliminar</a>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No tienes productos registrados.</p>";
    }
}

// **Función para mostrar las ventas del comunario**
function mostrarVentasComunario() {
    global $conn;

    // ID del comunario logueado
    $id_comunario = 1; // Ejemplo: El comunario con ID 1

    // Consulta para obtener las ventas de los productos del comunario
    $sql = "SELECT p.id_pedido_carrito, p.cantidad, p.estado_pedido, p.fecha_pedido, prod.nombre as producto, u.correo as comprador
            FROM PEDIDO_CARRITO p
                INNER JOIN PRODUCTO prod ON p.Id_producto = prod.Id_producto
                INNER JOIN USUARIO u ON p.id_comprador = u.id_usuario
            WHERE prod.id_comunario = $id_comunario";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Gestión de Ventas</h2>";
        echo "<table>
                <tr>
                    <th>ID Pedido</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Comprador</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["id_pedido"]."</td>
                    <td>".$row["producto"]."</td>
                    <td>".$row["cantidad"]."</td>
                    <td>".$row["comprador"]."</td>
                    <td>".$row["estado_pedido"]."</td>
                    <td>".$row["fecha_pedido"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No tienes ventas registradas.</p>";
    }
}

// **Función para mostrar el perfil de la comunidad del comunario**
function mostrarPerfilComunidad() {
    global $conn;

    // ID del comunario logueado
    $id_comunario = 1; // Ejemplo: El comunario con ID 1

    // Consulta para obtener la comunidad del comunario
    $sql = "SELECT c.nombre, c.nro_habitantes, d.nombre_departamento
            FROM COMUNIDAD c
            INNER JOIN DEPARTAMENTO d ON c.id_departamento = d.id_departamento
            INNER JOIN COMUNARIO cm ON cm.id_comunidad = c.id_comunidad
            WHERE cm.id_comunario = $id_comunario";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h2>Perfil de la Comunidad</h2>";
        echo "<p>Nombre de la Comunidad: " . $row["nombre"] . "</p>";
        echo "<p>Número de Habitantes: " . $row["nro_habitantes"] . "</p>";
        echo "<p>Departamento: " . $row["nombre_departamento"] . "</p>";
    } else {
        echo "<p>No se encontró información de la comunidad.</p>";
    }
}
?>
