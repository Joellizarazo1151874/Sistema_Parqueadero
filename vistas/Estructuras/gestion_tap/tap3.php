<div id="tab3" class="tab-content d-none">
    <div class="row">
        <!-- Columna del calendario -->
        <div class="col-md-3">
            <div class="card p-3">
                <h6 class="fw-bold text-center">marzo_2025</h6>
                <div id="calendar"></div>
            </div>
        </div>

        <!-- Columna principal -->
        <div class="col-md-9">
            <div class="ticket-summary card p-3">
                <!-- Filtros -->
                <div class="d-flex align-items-center gap-2 mb-3">
                    <select class="form-select" style="width: 180px;">
                        <option selected>Filtrar por Fecha</option>
                        <option>Historico</option>
                    </select>
                    <input type="text" class="form-control" placeholder="Matrícula">
                    <input type="text" class="form-control" placeholder="Ticket ID">
                    <input type="text" class="form-control" placeholder="Detalle">
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-share-alt"></i>
                    </button>
                </div>

                <!-- Tabla de tickets -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr class="table-light">
                                <th>E/S</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Detalle</th>
                                <th>Categoría</th>
                                <th>Ticket ID</th>
                                <th>Mensual ID</th>
                                <th>Operador</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="fas fa-arrow-right text-success"></i></td>
                                <td>13/03/25</td>
                                <td>09:26</td>
                                <td>#JOEL</td>
                                <td>Moto</td>
                                <td>101</td>
                                <td>--</td>
                                <td>joel lizarazo</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-arrow-right text-success"></i></td>
                                <td>13/03/25</td>
                                <td>09:26</td>
                                <td>#CARRO</td>
                                <td>Auto</td>
                                <td>102</td>
                                <td>--</td>
                                <td>joel lizarazo</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-arrow-left text-danger"></i></td>
                                <td>13/03/25</td>
                                <td>09:26</td>
                                <td>#CAMIONETA</td>
                                <td>Camioneta</td>
                                <td>103</td>
                                <td>--</td>
                                <td>joel lizarazo</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-arrow-right text-success"></i></td>
                                <td>13/03/25</td>
                                <td>09:27</td>
                                <td>#JOEL</td>
                                <td>Moto</td>
                                <td>101</td>
                                <td>--</td>
                                <td>joel lizarazo</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>