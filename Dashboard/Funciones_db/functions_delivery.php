<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "bdproduc_artesanales";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

function mostrarPedidosAsignados($id_delivery) {
    global $conn;
    $sql = "SELECT P.id_pedido_carrito, P.fecha_entrega, P.estado_entrega, C.nombre, C.apellido 
            FROM ENTREGA_DELIVERY P
            JOIN DELIVERY D ON P.id_delivery = D.id_delivery
            JOIN PEDIDO_CARRITO PD ON P.id_pedido_carrito = PD.id_pedido_carrito
            JOIN USUARIO C ON PD.id_comprador = C.id_usuario
            WHERE P.estado_entrega = 'pendiente' AND P.id_delivery = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_delivery);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Pedidos Asignados</h2>";
    echo "<table  class='w-full table-auto'>";
    echo "<thead>
            <tr class='bg-gray-200 text-gray-600 uppercase text-sm leading-normal'>
                <th>ID Pedido</th>
                <th>Fecha Pedido</th>
                <th>Estado</th>
                <th>Cliente</th>
                <th>Acciones</th>
            </tr>
        </thead><tbody>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".$row["id_pedido_carrito"]."</td>
                <td>".$row["fecha_entrega"]."</td>
                <td>".$row["estado_entrega"]."</td>
                <td>".$row["nombre"]." ".$row["apellido"]."</td>
                <td>
                    <form method='POST' action='acciones_delivery.php'>
                        <input type='hidden' name='id_pedido_carrito' value='".$row["id_pedido_carrito"]."'>
                        <select name='nuevo_estado'>
                            <option value='pendiente'>Pendiente</option>
                            <option value='entregado'>Entregado</option>
                        </select>
                        <button type='submit' name='actualizar_estado' class='btn btn-primary'>Actualizar</button>
                    </form>
                </td>
            </tr>";
    }
    echo "</tbody></table>";
}


function actualizarEstadoEntrega($id_pedido_carrito, $nuevo_estado) {
    global $conn;
    $sql = "UPDATE ENTREGA_DELIVERY SET estado_entrega = ? WHERE id_pedido_carrito = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nuevo_estado, $id_pedido_carrito);
    return $stmt->execute();
}

function mostrarHistorialEntregas($id_delivery) {
    global $conn;

    $sql = "SELECT E.id_identrega_delivery, E.fecha_entrega, PD.estado_pedido, C.nombre, C.apellido
            FROM ENTREGA_DELIVERY E
            JOIN DELIVERY D ON E.id_delivery = D.id_delivery
            JOIN PEDIDO_CARRITO PD ON E.id_pedido_carrito = PD.id_pedido_carrito
            JOIN USUARIO C ON PD.id_comprador = C.id_usuario
            WHERE D.id_delivery = ? AND E.estado_entrega = 'entregado'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_delivery);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Historial de Entregas</h2>";
    echo "<table class='w-full table-auto'>";
    echo "<thead>
            <tr class='bg-gray-200 text-gray-600 uppercase text-sm leading-normal'>
                <th>ID Pedido</th>
                <th>Datos Entrega</th>
                <th>Fecha Entrega</th>
                <th>Cliente</th>
            </tr>
            </thead><tbody>";
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row["id_entrega_delivery"]."</td>
                    <td>".$row["datos_entrega"]."</td>
                    <td>".$row["fecha_entrega"]."</td>
                    <td>".$row["nombre"]." ".$row["apellido"]."</td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No tienes entregas completadas.</td></tr>";
    }
    echo "</tbody></table>";
}

function mostrarPerfilDelivery($id_delivery) {
    global $conn;

    $sql = "SELECT U.nombre, U.apellido, U.correo, U.telefono
            FROM USUARIO U
            JOIN DELIVERY D ON U.id_usuario = D.id_delivery
            WHERE D.id_delivery = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_delivery);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h2>Perfil del Delivery</h2>";
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<p><strong>Nombre:</strong> ".$row["nombre"]." ".$row["apellido"]."</p>";
            echo "<p><strong>Correo:</strong> ".$row["correo"]."</p>";
            echo "<p><strong>Teléfono:</strong> ".$row["telefono"]."</p>";
        }
    } else {
        echo "<p>No se encontró información para este delivery.</p>";
    }
}
?>