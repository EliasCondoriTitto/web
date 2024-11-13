<?php
include("../includes/db.php");

if (isset($_GET['id_almacen'])) {
    $id_almacen = $_GET['id_almacen'];
    
    $query = "SELECT a.*, u.* 
              FROM almacen a 
              LEFT JOIN ubicacion u ON a.id_almacen = u.id_almacen 
              WHERE a.id_almacen = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_almacen);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result);
        $nombre = $row['nombre'];
        $departamento = $row['departamento'];
        $provincia = $row['provincia'];
        $calle = $row['calle'];
        $zona = $row['zona'];
        $nro_puerta = $row['nro_puerta'];
        $latitud = $row['latitud'];
        $longitud = $row['longitud'];
    }
    mysqli_stmt_close($stmt);
}

if (isset($_POST['update'])) {
    $id_almacen = $_GET['id_almacen'];
    $nombre = $_POST['nombre'];
    $departamento = $_POST['departamento'];
    $provincia = $_POST['provincia'];
    $calle = $_POST['calle'];
    $zona = $_POST['zona'];
    $nro_puerta = $_POST['nro_puerta'];
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];

    mysqli_begin_transaction($conn);

    try {
        // Actualizar tabla almacen
        $query_almacen = "UPDATE almacen SET nombre = ? WHERE id_almacen = ?";
        $stmt_almacen = mysqli_prepare($conn, $query_almacen);
        mysqli_stmt_bind_param($stmt_almacen, "si", $nombre, $id_almacen);
        
        if (!mysqli_stmt_execute($stmt_almacen)) {
            throw new Exception("Error al actualizar el almacén: " . mysqli_stmt_error($stmt_almacen));
        }

        // Actualizar tabla ubicacion
        $query_ubicacion = "UPDATE ubicacion 
                           SET departamento = ?, provincia = ?, calle = ?, 
                               zona = ?, nro_puerta = ?, latitud = ?, longitud = ? 
                           WHERE id_almacen = ?";
        $stmt_ubicacion = mysqli_prepare($conn, $query_ubicacion);
        mysqli_stmt_bind_param($stmt_ubicacion, "sssssdii", 
                              $departamento, $provincia, $calle, $zona, 
                              $nro_puerta, $latitud, $longitud, $id_almacen);
        
        if (!mysqli_stmt_execute($stmt_ubicacion)) {
            throw new Exception("Error al actualizar la ubicación: " . mysqli_stmt_error($stmt_ubicacion));
        }

        mysqli_commit($conn);
        $_SESSION['message'] = 'Almacén actualizado exitosamente';
        $_SESSION['message_type'] = 'success';
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['message'] = 'Error al actualizar el almacén: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    } finally {
        if (isset($stmt_almacen)) mysqli_stmt_close($stmt_almacen);
        if (isset($stmt_ubicacion)) mysqli_stmt_close($stmt_ubicacion);
    }

    header("Location: index.php");
}
?>

<?php include("../includes/header.php"); ?>

<div class="container p-4">
    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card card-body">
                <form action="edit.php?id_almacen=<?php echo $_GET['id_almacen']; ?>" method="POST">
                    <div class="form-group">
                        <input type="text" name="nombre" value="<?php echo $nombre; ?>" 
                               class="form-control" placeholder="Actualizar nombre">
                    </div>
                    <div class="form-group">
                        <select name="departamento" id="departamento" class="form-control" required>
                            <?php
                            $query_dept = "SELECT nombre_departamento FROM departamento";
                            $result_dept = mysqli_query($conn, $query_dept);
                            while($row_dept = mysqli_fetch_assoc($result_dept)) {
                                $selected = ($row_dept['nombre_departamento'] == $departamento) ? 'selected' : '';
                                echo "<option value='" . $row_dept['nombre_departamento'] . "' $selected>" 
                                     . $row_dept['nombre_departamento'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" name="provincia" value="<?php echo $provincia; ?>" 
                               class="form-control" placeholder="Provincia">
                    </div>
                    <div class="form-group">
                        <input type="text" name="calle" value="<?php echo $calle; ?>" 
                               class="form-control" placeholder="Calle">
                    </div>
                    <div class="form-group">
                        <input type="text" name="zona" value="<?php echo $zona; ?>" 
                               class="form-control" placeholder="Zona">
                    </div>
                    <div class="form-group">
                        <input type="text" name="nro_puerta" value="<?php echo $nro_puerta; ?>" 
                               class="form-control" placeholder="Número de puerta">
                    </div>
                    <div class="form-group">
                        <label for="latitud">Latitud:</label>
                        <input type="text" name="latitud" id="latitud" value="<?php echo $latitud; ?>" 
                               class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="longitud">Longitud:</label>
                        <input type="text" name="longitud" id="longitud" value="<?php echo $longitud; ?>" 
                               class="form-control" readonly>
                    </div>
                    <div id="map" style="height: 400px;" class="mb-3"></div>
                    <button class="btn btn-success btn-block" name="update">
                        Actualizar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var latitud = <?php echo $latitud; ?>;
    var longitud = <?php echo $longitud; ?>;
    
    var map = L.map('map').setView([latitud, longitud], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker = L.marker([latitud, longitud], {draggable: true}).addTo(map);

    marker.on('dragend', function(e) {
        var latlng = e.target.getLatLng();
        document.getElementById('latitud').value = latlng.lat.toFixed(6);
        document.getElementById('longitud').value = latlng.lng.toFixed(6);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('latitud').value = e.latlng.lat.toFixed(6);
        document.getElementById('longitud').value = e.latlng.lng.toFixed(6);
    });

    var departamentos = {
        'La Paz': [-16.5, -68.15],
        'Cochabamba': [-17.3895, -66.1568],
        'Santa Cruz': [-17.8146, -63.1561],
        'Oruro': [-17.9833, -67.15],
        'Potosí': [-19.5836, -65.7531],
        'Tarija': [-21.5355, -64.7296],
        'Chuquisaca': [-19.0368, -65.2627],
        'Beni': [-14.8333, -64.9],
        'Pando': [-11.0267, -68.7692]
    };

    document.getElementById('departamento').addEventListener('change', function() {
        var departamento = this.value;
        if (departamentos[departamento]) {
            var latlng = departamentos[departamento];
            map.setView(latlng, 8);
            marker.setLatLng(latlng);
            document.getElementById('latitud').value = latlng[0].toFixed(6);
            document.getElementById('longitud').value = latlng[1].toFixed(6);
        }
    });
});
</script>

<?php include("../includes/footer.php"); ?>