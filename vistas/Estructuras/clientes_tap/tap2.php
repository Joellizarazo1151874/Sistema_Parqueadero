<div id="tab2" class="tab-content d-none">
    <div class="row">
        <!-- Columna principal -->
        <div class="col-md-12">
            <div class="ticket-summary card p-3">
                <!-- Filtros -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <select class="form-select" style="width: 180px;">
                        <option selected>Filtrar por Fecha</option>
                        <option>Historico</option>
                    </select>
                    <input type="text" class="form-control" placeholder="Matrícula">
                    <input type="text" class="form-control" placeholder="Cliente">
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-share-alt"></i>
                    </button>
                </div>

                <!-- Tabla de tickets -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr class="table-light">
                                <th>ID</th>
                                <th>Matricula</th>
                                <th>Marca Modelo</th>
                                <th>Categoria</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>ABC-123</td>
                                <td>CARRO</td>
                                <td>ABC-123,BEC-231</td>
                                <td>ROJO</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>DBC-432</td>
                                <td>MOTO</td>
                                <td>BEC-231</td>
                                <td>CLARO</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>KDS-321</td>
                                <td>MOTO</td>
                                <td>AED-231</td>
                                <td>AMARILLO</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>