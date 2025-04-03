<?php
// Obtener los tipos de vehículos de la tabla tarifas
$sql_tipos_vehiculo = "SELECT DISTINCT tipo_vehiculo FROM tarifas";
$resultado_tipos_vehiculo = $conexion->query($sql_tipos_vehiculo);
$tipos_vehiculo = [];
if ($resultado_tipos_vehiculo->num_rows > 0) {
    while ($fila = $resultado_tipos_vehiculo->fetch_assoc()) {
        $tipos_vehiculo[] = $fila['tipo_vehiculo'];
    }
}

// Obtener los tipos de tarifa de la tabla tolerancia
$sql_tipos_tarifa = "SELECT tipo FROM tolerancia ORDER BY tipo";
$resultado_tipos_tarifa = $conexion->query($sql_tipos_tarifa);
$tipos_tarifa = [];
if ($resultado_tipos_tarifa && $resultado_tipos_tarifa->num_rows > 0) {
    while ($fila = $resultado_tipos_tarifa->fetch_assoc()) {
        $tipos_tarifa[] = $fila['tipo'];
    }
}

// Si no hay tipos de tarifa definidos, añadir al menos "hora" por defecto
if (empty($tipos_tarifa)) {
    $tipos_tarifa = ['hora'];
}

// Tipo de tarifa seleccionado (por defecto hora)
$tipo_tarifa_seleccionado = 'hora';
if (isset($_COOKIE['tipo_tarifa_seleccionado']) && in_array($_COOKIE['tipo_tarifa_seleccionado'], $tipos_tarifa)) {
    $tipo_tarifa_seleccionado = $_COOKIE['tipo_tarifa_seleccionado'];
}
?>

<!-- [ Main Content ] end -->
<footer class="pc-footer">
    <div class="footer-container">
        <div class="button-group">
            <!-- Selector de tipo de tarifa -->
            <div class="dropdown d-inline-block me-3">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="tipoTarifaDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    x<?php echo ucfirst($tipo_tarifa_seleccionado); ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="tipoTarifaDropdown">
                    <?php foreach ($tipos_tarifa as $tipo): ?>
                        <li><a class="dropdown-item tipo-tarifa-option" href="#" data-tipo="<?php echo $tipo; ?>"><?php echo ucfirst($tipo); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <span class="separator">|</span>
            <?php
            foreach ($tipos_vehiculo as $tipo) {
                echo '<button class="btn btn-custom btn-lightblue" data-bs-toggle="modal" data-bs-target="#' . strtolower($tipo) . 'Modal">' . strtoupper($tipo) . '</button>';
            }
            ?>
        </div>
    </div>
</footer>

<!-- Modal Moto -->
<div class="modal fade" id="motoModal" tabindex="-1" aria-labelledby="motoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="motoModalLabel">Ingreso x<span class="tipo-tarifa-texto"><?php echo ucfirst($tipo_tarifa_seleccionado); ?></span> - Moto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controladores/registro_parqueo.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="tipo_vehiculo" value="moto">
                    <input type="hidden" name="tipo_registro" class="tipo-tarifa-input" value="<?php echo $tipo_tarifa_seleccionado; ?>">
                    <div class="mb-3">
                        <label class="form-label">Ingrese la Matrícula</label>
                        <input type="text" class="form-control" name="matricula" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Ingresar x<span class="tipo-tarifa-texto"><?php echo ucfirst($tipo_tarifa_seleccionado); ?></span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Auto -->
<div class="modal fade" id="autoModal" tabindex="-1" aria-labelledby="autoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="autoModalLabel">Ingreso x<span class="tipo-tarifa-texto"><?php echo ucfirst($tipo_tarifa_seleccionado); ?></span> - Auto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controladores/registro_parqueo.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="tipo_vehiculo" value="auto">
                    <input type="hidden" name="tipo_registro" class="tipo-tarifa-input" value="<?php echo $tipo_tarifa_seleccionado; ?>">
                    <div class="mb-3">
                        <label class="form-label">Ingrese la Matrícula</label>
                        <input type="text" class="form-control" name="matricula" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Ingresar x<span class="tipo-tarifa-texto"><?php echo ucfirst($tipo_tarifa_seleccionado); ?></span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Camioneta -->
<div class="modal fade" id="camionetaModal" tabindex="-1" aria-labelledby="camionetaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="camionetaModalLabel">Ingreso x<span class="tipo-tarifa-texto"><?php echo ucfirst($tipo_tarifa_seleccionado); ?></span> - Camioneta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controladores/registro_parqueo.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="tipo_vehiculo" value="camioneta">
                    <input type="hidden" name="tipo_registro" class="tipo-tarifa-input" value="<?php echo $tipo_tarifa_seleccionado; ?>">
                    <div class="mb-3">
                        <label class="form-label">Ingrese la Matrícula</label>
                        <input type="text" class="form-control" name="matricula" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Ingresar x<span class="tipo-tarifa-texto"><?php echo ucfirst($tipo_tarifa_seleccionado); ?></span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Motocarro -->
<div class="modal fade" id="motocarroModal" tabindex="-1" aria-labelledby="motocarroModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="motocarroModalLabel">Ingreso x<span class="tipo-tarifa-texto"><?php echo ucfirst($tipo_tarifa_seleccionado); ?></span> - Motocarro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controladores/registro_parqueo.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="tipo_vehiculo" value="motocarro">
                    <input type="hidden" name="tipo_registro" class="tipo-tarifa-input" value="<?php echo $tipo_tarifa_seleccionado; ?>">
                    <div class="mb-3">
                        <label class="form-label">Ingrese la Matrícula</label>
                        <input type="text" class="form-control" name="matricula" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="ingreso_previo" id="ingresoPreviolMotocarro">
                        <label class="form-check-label" for="ingresoPreviolMotocarro">Ingreso Previo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Ingresar x<span class="tipo-tarifa-texto"><?php echo ucfirst($tipo_tarifa_seleccionado); ?></span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para manejar el cambio de tipo de tarifa -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener todos los elementos de opciones de tipo de tarifa
    const tipoTarifaOptions = document.querySelectorAll('.tipo-tarifa-option');
    
    // Manejar clic en opciones de tipo de tarifa
    tipoTarifaOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            
            const tipoSeleccionado = this.getAttribute('data-tipo');
            console.log('Tipo de tarifa seleccionado:', tipoSeleccionado);
            
            // Actualizar el texto del botón
            document.getElementById('tipoTarifaDropdown').textContent = 'x' + tipoSeleccionado.charAt(0).toUpperCase() + tipoSeleccionado.slice(1);
            
            // Actualizar todos los elementos de texto
            document.querySelectorAll('.tipo-tarifa-texto').forEach(elem => {
                elem.textContent = tipoSeleccionado.charAt(0).toUpperCase() + tipoSeleccionado.slice(1);
            });
            
            // Actualizar todos los campos ocultos
            document.querySelectorAll('.tipo-tarifa-input').forEach(input => {
                input.value = tipoSeleccionado;
                console.log('Campo actualizado:', input, 'valor:', tipoSeleccionado);
            });
            
            // Guardar la selección en una cookie para persistencia
            document.cookie = "tipo_tarifa_seleccionado=" + tipoSeleccionado + "; path=/; max-age=86400";
            console.log('Cookie guardada:', "tipo_tarifa_seleccionado=" + tipoSeleccionado);
            
            // Mostrar notificación de cambio
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Tipo de tarifa actualizado',
                    text: 'Se ha cambiado a tarifa por ' + tipoSeleccionado,
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                    toast: true
                });
            } else {
                alert('Tipo de tarifa cambiado a: ' + tipoSeleccionado);
            }
        });
    });
});
</script>
