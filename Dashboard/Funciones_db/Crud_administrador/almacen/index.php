<?php include('../includes/db.php'); ?>
<?php include('../includes/header.php'); ?>

<!-- Agregar los archivos CSS y JS de Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<main class="container p-4">
    <div class="row">
        <div class="col-md-4">
            <?php if(isset($_SESSION['message'])) { ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                    <?= $_SESSION['message'] ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php session_unset(); } ?>

            <div class="card card-body">
                <form action="save.php" method="POST" id="almacenForm">
                    <div class="form-group">
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre del Almacén" autofocus required>
                    </div>
                    
                    <div class="form-group">
                        <select name="departamento" id="departamento" class="form-control" required>
                            <option value="">Seleccione un departamento</option>
                            <?php
                            $query_dept = "SELECT nombre_departamento FROM departamento";
                            $result_dept = mysqli_query($conn, $query_dept);
                            while($row_dept = mysqli_fetch_assoc($result_dept)) {
                                echo "<option value='" . $row_dept['nombre_departamento'] . "'>" . $row_dept['nombre_departamento'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <input type="text" name="provincia" class="form-control" placeholder="Provincia" required>
                    </div>
                    
                    <div class="form-group">
                        <input type="text" name="calle" class="form-control" placeholder="Calle" required>
                    </div>
                    
                    <div class="form-group">
                        <input type="text" name="zona" class="form-control" placeholder="Zona" required>
                    </div>
                    
                    <div class="form-group">
                        <input type="text" name="nro_puerta" class="form-control" placeholder="Número de puerta" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="latitud">Latitud:</label>
                        <input type="text" name="latitud" id="latitud" class="form-control" readonly required>
                    </div>
                    
                    <div class="form-group">
                        <label for="longitud">Longitud:</label>
                        <input type="text" name="longitud" id="longitud" class="form-control" readonly required>
                    </div>
                    
                    <input type="submit" class="btn btn-success btn-block" name="save" value="Guardar Almacén">
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre Almacén</th>
                        <th>Departamento</th>
                        <th>Provincia</th>
                        <th>Dirección</th>
                        <th>Coordenadas</th>
                        <th>Fecha de Registro</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT a.id_almacen, a.nombre, a.fecha_registro, 
                                     u.departamento, u.provincia, u.calle, u.zona, u.nro_puerta, u.latitud, u.longitud
                              FROM almacen a
                              LEFT JOIN ubicacion u ON a.id_almacen = u.id_almacen";
                    $result_almacen = mysqli_query($conn, $query);

                    while($row = mysqli_fetch_assoc($result_almacen)) { ?>
                        <tr>
                            <td><?php echo $row['nombre']; ?></td>
                            <td><?php echo $row['departamento']; ?></td>
                            <td><?php echo $row['provincia']; ?></td>
                            <td><?php echo $row['calle'] . ' ' . $row['zona'] . ' #' . $row['nro_puerta']; ?></td>
                            <td><?php echo $row['latitud'] . ', ' . $row['longitud']; ?></td>
                            <td><?php echo $row['fecha_registro']; ?></td>
                            <td>
                                <a href="edit.php?id_almacen=<?php echo $row['id_almacen'] ?>" class="btn btn-secondary">Editar
                                </a>
                                <a href="delete.php?id_almacen=<?php echo $row['id_almacen'] ?>" class="btn btn-danger">Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Selecciona la ubicación del almacén</h5>
                    <div id="map" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
#map { 
    height: 400px; 
    width: 100%;
    z-index: 1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el mapa en el centro de Bolivia
    var map = L.map('map').setView([-16.290154, -63.588653], 5);
    
    // Agregar la capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker;

    // Función para actualizar o crear un marcador
    function updateMarker(latlng) {
        if (marker) {
            marker.setLatLng(latlng);
        } else {
            marker = L.marker(latlng, {draggable: true}).addTo(map);
            marker.on('dragend', function(e) {
                updateCoordinates(e.target.getLatLng());
            });
        }
        updateCoordinates(latlng);
    }

    // Función para actualizar las coordenadas mostradas
    function updateCoordinates(latlng) {
        document.getElementById('latitud').value = latlng.lat.toFixed(6);
        document.getElementById('longitud').value = latlng.lng.toFixed(6);
    }

    // Evento de clic en el mapa
    map.on('click', function(e) {
        updateMarker(e.latlng);
    });

    // Coordenadas aproximadas de los departamentos de Bolivia
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

    // Evento de cambio en el selector de departamento
    document.getElementById('departamento').addEventListener('change', function() {
        var departamento = this.value;
        if (departamentos[departamento]) {
            var latlng = departamentos[departamento];
            map.setView(latlng, 8);
            updateMarker(L.latLng(latlng[0], latlng[1]));
        }
    });
});
</script>

<?php include('../includes/footer.php'); ?>