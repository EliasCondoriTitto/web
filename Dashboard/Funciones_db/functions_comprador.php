
<?php
$servername = "localhost";
$username = "root"; // Cambia según tu configuración
$password = "";     // Cambia según tu configuración
$database = "bdproduc_artesanales";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

function verProductos() {
    global $conn;

    $sql = "SELECT * FROM PRODUCTO WHERE stock > 0";
    $result = $conn->query($sql);

    echo "<h2>Productos Disponibles</h2>";
    echo "<table class='w-full table-auto'>
            <thead>
                <tr class='bg-gray-200 text-gray-600 uppercase text-sm leading-normal'>
                    <th>Nombre</th>
                    <th>Características</th>
                    <th>Precio</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["nombre"]) . "</td>
                <td>" . htmlspecialchars($row["caracteristica"]) . "</td>
                <td>$" . number_format($row["precio"], 2) . "</td>
                <td>" . htmlspecialchars($row["stock"]) . "</td>
              </tr>";
    }
    echo "</tbody></table>";
}

function historialCompras($id_comprador) {
    global $conn;

    $sql = $conn->prepare("SELECT * FROM PEDIDO_CARRITO WHERE id_comprador = ?");
    $sql->bind_param("i", $id_comprador);
    $sql->execute();
    $result = $sql->get_result();

    echo "<h2>Historial de Compras</h2>";
    echo "<table class='w-full table-auto'>
            <thead>
                <tr class='bg-gray-200 text-gray-600 uppercase text-sm leading-normal'>
                    <th>ID Pedido</th>
                    <th>Fecha</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th>Costo de envio</th>
                    
                </tr>
            </thead>
            <tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["id_pedido_carrito"]) . "</td>
                <td>" . htmlspecialchars($row["fecha_pedido"]) . "</td>
                <td>" . htmlspecialchars($row["cantidad"]) . "</td>
                <td>" . htmlspecialchars($row["estado_pedido"]) . "</td>
                <td>BOB" . number_format($row["costo_envio"], 2) . "</td>
                
              </tr>";
    }
    echo "</tbody></table>";
}

function misPedidos($id_comprador) {
    global $conn;

    $sql = $conn->prepare("SELECT * FROM PEDIDO_CARRITO WHERE id_comprador = ? AND estado_pedido != 'entregado'");
    $sql->bind_param("i", $id_comprador);
    $sql->execute();
    $result = $sql->get_result();

    echo "<h2>Mis Pedidos Activos</h2>";
    echo "<table class='w-full table-auto'>
            <thead>
                <tr class='bg-gray-200 text-gray-600 uppercase text-sm leading-normal'>
                    <th>ID Pedido</th>
                    <th>Fecha</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["id_pedido_carrito"]) . "</td>
                <td>" . htmlspecialchars($row["fecha_pedido"]) . "</td>
                <td>" . htmlspecialchars($row["cantidad"]) . "</td>
                <td>" . htmlspecialchars($row["estado_pedido"]) . "</td>
              </tr>";
    }
    echo "</tbody></table>";
}

function misResenas($id_usuario) {
    global $conn;

    $sql = $conn->prepare("SELECT R.fecha_publicación, R.comentario, R.calificacion, P.nombre AS producto_nombre 
                           FROM RESEÑA R
                           JOIN PRODUCTO P ON R.id_producto = P.id_producto 
                           WHERE R.id_usuario = ?");
    $sql->bind_param("i", $id_usuario);
    $sql->execute();
    $result = $sql->get_result();

    echo "<h2>Mis Reseñas</h2>";
    echo "<table class='w-full table-auto'>
            <thead>
                <tr class='bg-gray-200 text-gray-600 uppercase text-sm leading-normal'>
                    <th>Producto</th>
                    <th>Fecha</th>
                    <th>Comentario</th>
                    <th>Calificación</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row["producto_nombre"]) . "</td>
                <td>" . htmlspecialchars($row["fecha_publicacion"]) . "</td>
                <td>" . htmlspecialchars($row["comentario"]) . "</td>
                <td>" . htmlspecialchars($row["calificacion"]) . "</td>
              </tr>";
    }
    echo "</tbody></table>";
}



function mostrarNotificaciones($id_usuario) {
    global $conn;
    
    $sql = "SELECT mensaje, fecha_creacion FROM NOTIFICACION WHERE id_usuario = ? ORDER BY fecha_creacion DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Notificaciones</h2>";
    echo "<ul class='list-group'>";
    while($row = $result->fetch_assoc()) {
        echo "<li class='list-group-item'><strong>".$row['fecha_creacion']."</strong> - ".$row['mensaje']."</li>";
    }
    echo "</ul>";
}

mostrarNotificaciones($id_usuario);

?>
