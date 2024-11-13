<?php
include("../includes/db.php");
include("auth_products.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['id_usuario'];
$user_role = getUserRole($conn, $user_id);

if (isset($_POST['save'])) {
    $nombre = $_POST['nombre'];
    $caracteristica = $_POST['caracteristica'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $id_categoria = $_POST['id_categoria'];
    $id_almacen = $_POST['id_almacen'];
    $fecha_creacion = date("Y-m-d H:i:s");

    // Determinar el id_comunario basado en el rol del usuario
    if ($user_role === 'administrador') {
        // El administrador puede crear productos para cualquier comunario
        $id_comunario = $_POST['id_comunario'];
    } else {
        // Los comunarios solo pueden crear productos para sí mismos
        $id_comunario = $user_id;
    }

    // Manejo de las imágenes
    $imagenes = [];
    if (isset($_FILES['imagenes'])) {
        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                $file_name = $_FILES['imagenes']['name'][$key];
                $file_tmp = $_FILES['imagenes']['tmp_name'][$key];
                
                // Generar un nombre único para el archivo
                $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $nuevo_nombre = uniqid() . '.' . $extension;
                
                $upload_dir = "../uploads/";
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_path = $upload_dir . $nuevo_nombre;
                
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $imagenes[] = $file_path;
                }
            }
        }
    }
    
    $imagenes_serializadas = !empty($imagenes) ? serialize($imagenes) : NULL;

    // Preparar la consulta usando declaraciones preparadas para mayor seguridad
    $query = "INSERT INTO producto (nombre, caracteristica, precio, stock, fecha_creacion, imagenes, id_comunario, id_categoria) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssddssii", $nombre, $caracteristica, $precio, $stock, $fecha_creacion, $imagenes_serializadas, $id_comunario, $id_categoria);
    
    if ($stmt->execute()) {
        $id_producto = $stmt->insert_id;
        
        // Insertar en la tabla ESTA para relacionar el producto con el almacén
        $query_esta = "INSERT INTO esta (id_producto, id_almacen) VALUES (?, ?)";
        $stmt_esta = $conn->prepare($query_esta);
        $stmt_esta->bind_param("ii", $id_producto, $id_almacen);
        
        if ($stmt_esta->execute()) {
            $_SESSION['message'] = 'Producto guardado correctamente';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error al asignar el producto al almacén: ' . $stmt_esta->error;
            $_SESSION['message_type'] = 'danger';
        }
        
        $stmt_esta->close();
    } else {
        $_SESSION['message'] = 'Error al guardar el producto: ' . $stmt->error;
        $_SESSION['message_type'] = 'danger';
    }

    $stmt->close();
    header("Location: index.php");
    exit();
}
?>