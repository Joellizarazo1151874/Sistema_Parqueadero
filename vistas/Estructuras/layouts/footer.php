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
$sql_tipos_tarifa = "SELECT tipo, tolerancia, tiempo FROM tolerancia ORDER BY tipo";
$resultado_tipos_tarifa = $conexion->query($sql_tipos_tarifa);
$tipos_tarifa = [];
$tiempos_tarifa = []; // Nuevo array para almacenar los tiempos de cada tipo
if ($resultado_tipos_tarifa && $resultado_tipos_tarifa->num_rows > 0) {
    while ($fila = $resultado_tipos_tarifa->fetch_assoc()) {
        $tipos_tarifa[] = $fila['tipo'];
        $tiempos_tarifa[$fila['tipo']] = $fila['tiempo']; // Guardar el tiempo asociado a cada tipo
    }
}

// Si no hay tipos de tarifa definidos, añadir al menos "hora" por defecto
if (empty($tipos_tarifa)) {
    $tipos_tarifa = ['hora'];
    $tiempos_tarifa['hora'] = 1; // Tiempo por defecto para hora = 1 hora
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
                <button class="btn btn-sm btn-primary" type="button" id="tipoTarifaDropdown" 
                    onclick="toggleTipoTarifaMenu()" style="position: relative; z-index: 1000; padding: 8px 15px; font-weight: bold;">
                    <i class="ti ti-clock me-1"></i>x<?php echo ucfirst($tipo_tarifa_seleccionado); ?> <i class="ti ti-chevron-up ms-1"></i>
                </button>
                <div id="tipoTarifaMenu" class="position-absolute bg-white shadow-lg rounded p-2" 
                    style="display: none; min-width: 150px; z-index: 1001; bottom: 100%; left: 0; margin-bottom: 5px; border: 1px solid #e9ecef; border-radius: 0.375rem; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;">
                    <?php 
                    // Mostrar cada tipo de tarifa disponible como una opción en el menú
                    foreach ($tipos_tarifa as $tipo): 
                        $active = ($tipo === $tipo_tarifa_seleccionado) ? ' fw-bold text-primary' : '';
                    ?>
                        <div class="px-3 py-2 tipo-tarifa-item" style="cursor: pointer; transition: background-color 0.2s;" 
                             onmouseover="this.style.backgroundColor='#f8f9fa'" 
                             onmouseout="this.style.backgroundColor='transparent'">
                            <a href="javascript:void(0)" class="text-decoration-none tipo-tarifa-option<?php echo $active; ?>" 
                               data-tipo="<?php echo $tipo; ?>" 
                               onclick="cambiarTipoTarifa('<?php echo $tipo; ?>'); return false;" style="display: block;">
                                <i class="ti ti-<?php echo ($tipo === 'hora') ? 'clock' : 'calendar'; ?> me-2"></i><?php echo ucfirst($tipo); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
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
// Función para alternar la visibilidad del menú de tipos de tarifa
function toggleTipoTarifaMenu() {
    const menu = document.getElementById('tipoTarifaMenu');
    if (menu) {
        if (menu.style.display === 'none' || menu.style.display === '') {
            menu.style.display = 'block';
            console.log('Menú abierto');
        } else {
            menu.style.display = 'none';
            console.log('Menú cerrado');
        }
    } else {
        console.error('No se encontró el menú de tipos de tarifa');
    }
}

// Función para actualizar el tipo de tarifa
function cambiarTipoTarifa(tipo) {
    console.log('Cambiando tipo de tarifa a:', tipo);
    
    // Ocultar el menú después de seleccionar
    const menu = document.getElementById('tipoTarifaMenu');
    if (menu) {
        menu.style.display = 'none';
    }
    
    // Determinar ícono según el tipo
    const icono = tipo === 'hora' ? 'clock' : 'calendar';
    
    // Actualizar el texto del botón
    document.getElementById('tipoTarifaDropdown').innerHTML = '<i class="ti ti-' + icono + ' me-1"></i>x' + tipo.charAt(0).toUpperCase() + tipo.slice(1) + ' <i class="ti ti-chevron-up ms-1"></i>';
    
    // Actualizar todos los elementos de texto
    document.querySelectorAll('.tipo-tarifa-texto').forEach(elem => {
        elem.textContent = tipo.charAt(0).toUpperCase() + tipo.slice(1);
    });
    
    // Actualizar todos los campos ocultos
    document.querySelectorAll('.tipo-tarifa-input').forEach(input => {
        input.value = tipo;
        console.log('Campo actualizado:', input, 'valor:', tipo);
    });
    
    // Guardar la selección en una cookie para persistencia
    document.cookie = "tipo_tarifa_seleccionado=" + tipo + "; path=/; max-age=86400";
    console.log('Cookie guardada:', "tipo_tarifa_seleccionado=" + tipo);
    
    // Mostrar notificación de cambio
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Tipo de tarifa actualizado',
            text: 'Se ha cambiado a tarifa por ' + tipo,
            icon: 'success',
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            toast: true
        });
    } else {
        alert('Tipo de tarifa cambiado a: ' + tipo);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando selector de tipo de tarifa');
    
    // Cerrar el menú cuando se hace clic fuera de él
    document.addEventListener('click', function(e) {
        const menu = document.getElementById('tipoTarifaMenu');
        const toggleButton = document.getElementById('tipoTarifaDropdown');
        
        if (menu && menu.style.display === 'block' && 
            e.target !== menu && 
            e.target !== toggleButton && 
            !menu.contains(e.target) && 
            !toggleButton.contains(e.target)) {
            menu.style.display = 'none';
            console.log('Menú cerrado por clic fuera');
        }
    });
    
    // Verificar si hay un tipo de tarifa en la cookie
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
    
    const savedType = getCookie('tipo_tarifa_seleccionado');
    if (savedType) {
        console.log('Tipo de tarifa guardado en cookie:', savedType);
        
        // Determinar ícono según el tipo guardado
        const savedIcono = savedType === 'hora' ? 'clock' : 'calendar';
        
        // Aplicar el tipo guardado sin mostrar notificación
        const dropdownBtn = document.getElementById('tipoTarifaDropdown');
        if (dropdownBtn) {
            dropdownBtn.innerHTML = '<i class="ti ti-' + savedIcono + ' me-1"></i>x' + savedType.charAt(0).toUpperCase() + savedType.slice(1) + ' <i class="ti ti-chevron-up ms-1"></i>';
        }
        
        // Actualizar todos los elementos de texto
        document.querySelectorAll('.tipo-tarifa-texto').forEach(elem => {
            elem.textContent = savedType.charAt(0).toUpperCase() + savedType.slice(1);
        });
        
        // Actualizar todos los campos ocultos
        document.querySelectorAll('.tipo-tarifa-input').forEach(input => {
            input.value = savedType;
        });
    }
});
</script>

<!-- El script de cámara persistente ya está cargado en el header -->


