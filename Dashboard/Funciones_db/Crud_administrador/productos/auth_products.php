<?php
function getUserRole($conn, $user_id) {
    $query = "SELECT rol FROM usuario WHERE id_usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user['rol'];
}

function canManageProduct($conn, $user_id, $product_id = null) {
    $role = getUserRole($conn, $user_id);
    
    if ($role === 'administrador') {
        return true; // El administrador puede manejar todos los productos
    }
    
    if ($role === 'comunario') {
        if ($product_id === null) {
            return true; // Comunario puede crear nuevos productos
        }
        
        // Verificar si el producto pertenece al comunario
        $query = "SELECT id_comunario FROM producto WHERE id_producto = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        return $product['id_comunario'] === $user_id;
    }
    
    return false;
}

function getProductsForUser($conn, $user_id) {
    $role = getUserRole($conn, $user_id);
    
    if ($role === 'administrador') {
        // El administrador ve todos los productos
        $query = "SELECT p.*, u.nombre AS nombre_comunario, u.apellido AS apellido_comunario, 
                  c.nombre_categoria, a.nombre AS nombre_almacen
                  FROM producto p 
                  JOIN usuario u ON p.id_comunario = u.id_usuario 
                  JOIN categoria c ON p.id_categoria = c.id_categoria
                  LEFT JOIN esta e ON p.id_producto = e.id_producto
                  LEFT JOIN almacen a ON e.id_almacen = a.id_almacen";
        return mysqli_query($conn, $query);
    } else {
        // El comunario solo ve sus productos
        $query = "SELECT p.*, u.nombre AS nombre_comunario, u.apellido AS apellido_comunario, 
                  c.nombre_categoria, a.nombre AS nombre_almacen
                  FROM producto p 
                  JOIN usuario u ON p.id_comunario = u.id_usuario 
                  JOIN categoria c ON p.id_categoria = c.id_categoria
                  LEFT JOIN esta e ON p.id_producto = e.id_producto
                  LEFT JOIN almacen a ON e.id_almacen = a.id_almacen
                  WHERE p.id_comunario = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>