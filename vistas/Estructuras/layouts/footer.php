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
?>

<!-- [ Main Content ] end -->
<footer class="pc-footer">
    <div class="footer-container">
        <div class="button-group">
            <span class="fw-bold">xHora</span>
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
                <h5 class="modal-title" id="motoModalLabel">Ingreso x Hora - Moto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controladores/registro_parqueo.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="tipo_vehiculo" value="moto">
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
                    <button type="submit" class="btn btn-primary w-100">Ingresar xHora</button>
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
                <h5 class="modal-title" id="autoModalLabel">Ingreso x Hora - Auto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controladores/registro_parqueo.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="tipo_vehiculo" value="auto">
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
                    <button type="submit" class="btn btn-primary w-100">Ingresar xHora</button>
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
                <h5 class="modal-title" id="camionetaModalLabel">Ingreso x Hora - Camioneta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controladores/registro_parqueo.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="tipo_vehiculo" value="camioneta">
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
                    <button type="submit" class="btn btn-primary w-100">Ingresar xHora</button>
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
                <h5 class="modal-title" id="motocarroModalLabel">Ingreso x Hora - Motocarro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controladores/registro_parqueo.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="tipo_vehiculo" value="motocarro">
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
                    <button type="submit" class="btn btn-primary w-100">Ingresar xHora</button>
                </div>
            </form>
        </div>
    </div>
</div>
