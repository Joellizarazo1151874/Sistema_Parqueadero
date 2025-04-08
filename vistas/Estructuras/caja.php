<?php
session_start();
// Configurar la zona horaria para Colombia
date_default_timezone_set('America/Bogota');
include '../../modelo/conexion.php';
include '../../controladores/seguridad.php';
?>
<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>Home | SmartPark</title>
  <!-- [Meta] -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description"
    content="Mantis is made using Bootstrap 5 design framework. Download the free admin template & use it for your project.">
  <meta name="keywords"
    content="Mantis, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Bootstrap Admin Template">
  <meta name="author" content="CodedThemes">

  <!-- [Favicon] icon -->
  <link rel="icon" href="../assets/images/favicon.svg" type="image/x-icon"> <!-- [Google Font] Family -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
    id="main-font-link">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- [Tabler Icons] https://tablericons.com -->
  <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
  <!-- [Feather Icons] https://feathericons.com -->
  <link rel="stylesheet" href="../assets/fonts/feather.css">
  <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
  <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
  <!-- [Material Icons] https://fonts.google.com/icons -->
  <link rel="stylesheet" href="../assets/fonts/material.css">
  <!-- [Template CSS Files] -->
  <link rel="stylesheet" href="../assets/css/style.css" id="main-style-link">
  <link rel="stylesheet" href="../assets/css/style-preset.css">
  
  <!-- jQuery y SweetAlert2 -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- Estilos para notificaciones toast -->
  <style>
    .colored-toast {
      border-radius: 8px !important;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
      padding: 12px !important;
      width: 300px !important;
      max-width: 90vw !important;
    }
    
    .colored-toast.swal2-icon-success {
      background-color: #a5dc86 !important;
      color: #fff !important;
    }
    
    .colored-toast.swal2-icon-error {
      background-color: #f27474 !important;
      color: #fff !important;
    }
    
    .colored-toast.swal2-icon-warning {
      background-color: #f8bb86 !important;
      color: #fff !important;
    }
    
    .colored-toast.swal2-icon-info {
      background-color: #3fc3ee !important;
      color: #fff !important;
    }
    
    .colored-toast .swal2-title,
    .colored-toast .swal2-content {
      color: #fff !important;
    }
    
    .colored-toast .swal2-icon {
      margin: 0 !important;
      color: #fff !important;
      border-color: #fff !important;
    }
    
    .toast-title {
      font-size: 16px !important;
      font-weight: 600 !important;
      margin-bottom: 4px !important;
    }
    
    .toast-content {
      font-size: 14px !important;
    }
  </style>

</head>
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->
  <!-- [ Sidebar Menu ] start -->
  <?php include 'layouts/menu.php'; ?>
  <?php include 'layouts/header.php'; ?>

  <!-- [ Main Content ] start -->
  <div class="pc-container">
    <div class="pc-content">
      <!-- [ breadcrumb ] start -->
      <div class="mt-3">
        <!-- Menú de Navegación -->
        <ul class="nav nav-tabs justify-content-center" id="menu-tabs">
          <li class="nav-item">
            <a class="nav-link active" data-tab="tab1" href="#">Caja</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab2" href="#">Recibos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab3" href="#">Cierre de Caja</a>
          </li>
        </ul>

        <!-- Contenido dinámico -->
        <div class="tab-container mt-4">
          <div id="tab1" class="tab-content active">
            <div class="row">
              <!-- Resumen de Caja -->
              <div class="col-md-4">
                <div class="card shadow-sm border-0">
                  <div class="card-header text-white py-3">
                    <div class="d-flex align-items-center">
                      <span class="rounded-circle bg-light p-2 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-chart-pie text-dark"></i>
                      </span>
                      <h4 class="mb-0">Resumen de Caja</h4>
                    </div>
                  </div>
                  <div class="card-body p-0">
                    <!-- Tickets Activos -->
                    <div class="p-3 border-bottom d-flex align-items-center">
                      <div class="rounded-circle bg-primary text-white p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-ticket-alt fa-2x"></i>
                      </div>
                      <div>
                        <h6 class="text-muted mb-1">Tickets Activos</h6>
                        <h4 class="mb-0" id="total-tickets">0</h4>
                      </div>
                    </div>
                    
                    <!-- Total Efectivo -->
                    <div class="p-3 border-bottom d-flex align-items-center">
                      <div class="rounded-circle bg-success text-white p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                      </div>
                      <div>
                        <h6 class="text-muted mb-1">Total Efectivo</h6>
                        <h4 class="mb-0" id="total-efectivo">$0</h4>
                      </div>
                    </div>
                    
                    <!-- Total Tarjeta -->
                    <div class="p-3 border-bottom d-flex align-items-center">
                      <div class="rounded-circle bg-info text-white p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-credit-card fa-2x"></i>
                      </div>
                      <div>
                        <h6 class="text-muted mb-1">Total Tarjeta</h6>
                        <h4 class="mb-0" id="total-tarjeta">$0</h4>
                      </div>
                    </div>
                    
                    <!-- Total Transferencia -->
                    <div class="p-3 d-flex align-items-center">
                      <div class="rounded-circle bg-warning text-white p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-exchange-alt fa-2x"></i>
                      </div>
                      <div>
                        <h6 class="text-muted mb-1">Total Transferencia</h6>
                        <h4 class="mb-0" id="total-transferencia">$0</h4>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Lista de Tickets Activos -->
              <div class="col-md-8">
                <div class="card">
                  <div class="card-header">
                    <h5>Tickets Activos</h5>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="tabla-tickets">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Placa</th>
                            <th>Tipo Vehículo</th>
                            <th>Tipo Tiempo</th>
                            <th>Cliente</th>
                            <th>Ingreso</th>
                            <th>Tiempo</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody id="lista-tickets">
                          <!-- Los tickets se cargarán dinámicamente aquí -->
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="tab2" class="tab-content d-none">
            <h3>Recibos</h3>
            <div class="card">
              <div class="card-header">
                <h5>Tickets Cerrados</h5>
              </div>
              <div class="card-body">
                <!-- Filtros de búsqueda -->
                <div class="row mb-3">
                  <div class="col-md-12">
                    <form id="formBusquedaRecibos" method="GET" class="d-flex flex-wrap gap-2">
                      <input type="hidden" name="tab" value="tab2">
                      <div class="input-group" style="max-width: 200px;">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        <input type="date" class="form-control" id="fecha_busqueda" name="fecha_busqueda" value="<?php echo isset($_GET['fecha_busqueda']) ? htmlspecialchars($_GET['fecha_busqueda']) : ''; ?>">
                      </div>
                      <div class="input-group" style="max-width: 200px;">
                        <span class="input-group-text"><i class="fas fa-car"></i></span>
                        <input type="text" class="form-control" name="placa_busqueda" placeholder="Placa" value="<?php echo isset($_GET['placa_busqueda']) ? htmlspecialchars($_GET['placa_busqueda']) : ''; ?>">
                      </div>
                      <div class="input-group" style="max-width: 200px;">
                        <span class="input-group-text"><i class="fas fa-tag"></i></span>
                        <select class="form-select" name="tipo_busqueda">
                          <option value="">Todos los tipos</option>
                          <?php
                          // Consulta para obtener tipos de vehículos únicos desde la tabla tarifas
                          $sql_tipos = "SELECT DISTINCT tipo_vehiculo FROM tarifas";
                          $result_tipos = $conexion->query($sql_tipos);
                          if ($result_tipos && $result_tipos->num_rows > 0) {
                            while ($row_tipo = $result_tipos->fetch_assoc()) {
                              $selected = (isset($_GET['tipo_busqueda']) && $_GET['tipo_busqueda'] == $row_tipo['tipo_vehiculo']) ? 'selected' : '';
                              echo '<option value="' . htmlspecialchars($row_tipo['tipo_vehiculo']) . '" ' . $selected . '>' . htmlspecialchars(ucfirst($row_tipo['tipo_vehiculo'])) . '</option>';
                            }
                          }
                          ?>
                        </select>
                      </div>
                      <div class="input-group" style="max-width: 200px;">
                        <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                        <select class="form-select" name="metodo_pago_busqueda">
                          <option value="">Todos los métodos</option>
                          <?php
                          // Incluir el controlador de métodos de pago
                          include_once '../../controladores/obtener_metodos_pago.php';
                          
                          // Obtener métodos de pago desde la base de datos
                          $metodos_pago = obtenerMetodosPago();
                          
                          // Mostrar opciones dinámicamente
                          foreach ($metodos_pago as $metodo) {
                            $selected = (isset($_GET['metodo_pago_busqueda']) && $_GET['metodo_pago_busqueda'] == $metodo['nombre']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($metodo['nombre']) . '" ' . $selected . '>' . htmlspecialchars($metodo['nombre']) . '</option>';
                          }
                          ?>
                        </select>
                      </div>
                      <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Buscar
                      </button>
                      <?php
                      // Mostrar el botón Limpiar solo si hay algún parámetro de búsqueda
                      $hayBusqueda = isset($_GET['fecha_busqueda']) || isset($_GET['placa_busqueda']) || isset($_GET['tipo_busqueda']) || isset($_GET['metodo_pago_busqueda']);
                      $hayValorBusqueda = !empty($_GET['fecha_busqueda']) || !empty($_GET['placa_busqueda']) || !empty($_GET['tipo_busqueda']) || !empty($_GET['metodo_pago_busqueda']);
                      if ($hayBusqueda && $hayValorBusqueda) {
                      ?>
                      <button type="button" id="limpiarFiltros" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpiar
                      </button>
                      <?php } ?>
                    </form>
                  </div>
                </div>

                <!-- Tabla de recibos -->
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Placa</th>
                        <th>Tipo</th>
                        <th>Ingreso</th>
                        <th>Salida</th>
                        <th>Tiempo</th>
                        <th>Método Pago</th>
                        <th>Total</th>
                        <th>Operador</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody id="tablaRecibos">
                      <?php
                      // Configuración de paginación
                      $registros_por_pagina = 10;
                      $pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
                      $offset = ($pagina_actual - 1) * $registros_por_pagina;

                      // Construir la consulta SQL base
                      $sql_recibos = "SELECT r.*, v.placa, v.tipo as tipo_vehiculo, mp.nombre as nombre_metodo_pago
                                      FROM registros_parqueo r 
                                      LEFT JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
                                      LEFT JOIN metodos_pago mp ON r.metodo_pago = mp.id_metodo
                                      WHERE r.estado = 'cerrado'";
                      
                      // Agregar filtros si existen
                      if (isset($_GET['fecha_busqueda']) && !empty($_GET['fecha_busqueda'])) {
                        $fecha_busqueda = $conexion->real_escape_string($_GET['fecha_busqueda']);
                        // Convertimos la fecha al formato correcto para MySQL y aseguramos que filtre por todo el día
                        $sql_recibos .= " AND DATE(r.hora_salida) = DATE('$fecha_busqueda')";
                      }
                      
                      if (isset($_GET['placa_busqueda']) && !empty($_GET['placa_busqueda'])) {
                        $placa_busqueda = $conexion->real_escape_string($_GET['placa_busqueda']);
                        $sql_recibos .= " AND v.placa LIKE '%$placa_busqueda%'";
                      }
                      
                      if (isset($_GET['tipo_busqueda']) && !empty($_GET['tipo_busqueda'])) {
                        $tipo_busqueda = $conexion->real_escape_string($_GET['tipo_busqueda']);
                        $sql_recibos .= " AND v.tipo = '$tipo_busqueda'";
                      }
                      
                      if (isset($_GET['metodo_pago_busqueda']) && !empty($_GET['metodo_pago_busqueda'])) {
                        $metodo_pago_busqueda = $conexion->real_escape_string($_GET['metodo_pago_busqueda']);
                        $sql_recibos .= " AND mp.nombre = '$metodo_pago_busqueda'";
                      }
                      
                      // Ordenar por fecha de salida más reciente
                      $sql_recibos .= " ORDER BY r.hora_salida DESC";
                      
                      // Consulta para contar el total de registros (para paginación)
                      $sql_total = "SELECT COUNT(*) as total FROM ($sql_recibos) as subconsulta";
                      $resultado_total = $conexion->query($sql_total);
                      $fila_total = $resultado_total->fetch_assoc();
                      $total_registros = $fila_total['total'];
                      $total_paginas = ceil($total_registros / $registros_por_pagina);
                      
                      // Agregar límite para paginación
                      $sql_recibos .= " LIMIT $offset, $registros_por_pagina";
                      
                      // Ejecutar la consulta
                      $resultado_recibos = $conexion->query($sql_recibos);
                      
                      if ($resultado_recibos && $resultado_recibos->num_rows > 0) {
                        while ($row = $resultado_recibos->fetch_assoc()) {
                          // Calcular tiempo de estancia
                          $hora_ingreso = new DateTime($row['hora_ingreso']);
                          $hora_salida = new DateTime($row['hora_salida']);
                          $diferencia = $hora_ingreso->diff($hora_salida);
                          
                          // Formatear tiempo de estancia
                          $tiempo_estancia = '';
                          if ($diferencia->days > 0) {
                            $tiempo_estancia .= $diferencia->days . 'd ';
                          }
                          $tiempo_estancia .= $diferencia->h . 'h ' . $diferencia->i . 'm';
                          
                          // Formatear el total pagado según las preferencias del usuario
                          $total_pagado = '$' . number_format($row['total_pagado'], 0, '', ',');
                          
                          echo '<tr>';
                          echo '<td>' . $row['id_registro'] . '</td>';
                          echo '<td>' . htmlspecialchars($row['placa']) . '</td>';
                          echo '<td>' . htmlspecialchars($row['tipo_vehiculo']) . '</td>';
                          echo '<td>' . date('d/m/Y H:i', strtotime($row['hora_ingreso'])) . '</td>';
                          echo '<td>' . date('d/m/Y H:i', strtotime($row['hora_salida'])) . '</td>';
                          echo '<td>' . $tiempo_estancia . '</td>';
                          echo '<td>' . htmlspecialchars($row['nombre_metodo_pago'] ?? 'No especificado') . '</td>';
                          echo '<td>' . $total_pagado . '</td>';
                          echo '<td>' . htmlspecialchars($row['cerrado_por']) . '</td>';
                          echo '<td>
                                  <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-info ver-recibo" data-id="' . $row['id_registro'] . '" style="width: 38px; height: 31px; display: flex; align-items: center; justify-content: center;">
                                      <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary imprimir-recibo" data-id="' . $row['id_registro'] . '" style="width: 38px; height: 31px; display: flex; align-items: center; justify-content: center;">
                                      <i class="fas fa-print"></i>
                                    </button>
                                  </div>
                                </td>';
                          echo '</tr>';
                        }
                      } else {
                        echo '<tr><td colspan="10" class="text-center">No se encontraron recibos</td></tr>';
                      }
                      ?>
                    </tbody>
                  </table>
                </div>

                <!-- Paginación -->
                <?php if ($total_paginas > 1): ?>
                <div class="d-flex justify-content-center mt-3">
                  <nav aria-label="Navegación de página">
                    <ul class="pagination">
                      <li class="page-item <?php echo ($pagina_actual <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?tab=tab2<?php echo isset($_GET['fecha_busqueda']) ? '&fecha_busqueda=' . htmlspecialchars($_GET['fecha_busqueda']) : ''; ?><?php echo isset($_GET['placa_busqueda']) ? '&placa_busqueda=' . htmlspecialchars($_GET['placa_busqueda']) : ''; ?><?php echo isset($_GET['tipo_busqueda']) ? '&tipo_busqueda=' . htmlspecialchars($_GET['tipo_busqueda']) : ''; ?><?php echo isset($_GET['metodo_pago_busqueda']) ? '&metodo_pago_busqueda=' . htmlspecialchars($_GET['metodo_pago_busqueda']) : ''; ?>&pagina=<?php echo $pagina_actual - 1; ?>" aria-label="Anterior">
                          <span aria-hidden="true">&laquo;</span>
                        </a>
                      </li>
                      
                      <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?php echo ($pagina_actual == $i) ? 'active' : ''; ?>">
                          <a class="page-link" href="?tab=tab2<?php echo isset($_GET['fecha_busqueda']) ? '&fecha_busqueda=' . htmlspecialchars($_GET['fecha_busqueda']) : ''; ?><?php echo isset($_GET['placa_busqueda']) ? '&placa_busqueda=' . htmlspecialchars($_GET['placa_busqueda']) : ''; ?><?php echo isset($_GET['tipo_busqueda']) ? '&tipo_busqueda=' . htmlspecialchars($_GET['tipo_busqueda']) : ''; ?><?php echo isset($_GET['metodo_pago_busqueda']) ? '&metodo_pago_busqueda=' . htmlspecialchars($_GET['metodo_pago_busqueda']) : ''; ?>&pagina=<?php echo $i; ?>">
                            <?php echo $i; ?>
                          </a>
                        </li>
                      <?php endfor; ?>
                      
                      <li class="page-item <?php echo ($pagina_actual >= $total_paginas) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?tab=tab2<?php echo isset($_GET['fecha_busqueda']) ? '&fecha_busqueda=' . htmlspecialchars($_GET['fecha_busqueda']) : ''; ?><?php echo isset($_GET['placa_busqueda']) ? '&placa_busqueda=' . htmlspecialchars($_GET['placa_busqueda']) : ''; ?><?php echo isset($_GET['tipo_busqueda']) ? '&tipo_busqueda=' . htmlspecialchars($_GET['tipo_busqueda']) : ''; ?><?php echo isset($_GET['metodo_pago_busqueda']) ? '&metodo_pago_busqueda=' . htmlspecialchars($_GET['metodo_pago_busqueda']) : ''; ?>&pagina=<?php echo $pagina_actual + 1; ?>" aria-label="Siguiente">
                          <span aria-hidden="true">&raquo;</span>
                        </a>
                      </li>
                    </ul>
                  </nav>
                </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Modal para ver detalles del recibo -->
            <div class="modal fade" id="modalDetalleRecibo" tabindex="-1" aria-labelledby="modalDetalleReciboLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalDetalleReciboLabel">Detalle del Recibo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body" id="contenidoDetalleRecibo">
                    <div class="text-center">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                      </div>
                      <p>Cargando detalles del recibo...</p>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnImprimirReciboModal">Imprimir</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="tab3" class="tab-content d-none">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Cierre de Caja</h3>
              </div>
              <div class="card-body">
                <div class="row mb-4">
                  <div class="col-md-6">
                    <form id="formCierreCaja" action="../../controladores/generar_reporte_caja.php" method="POST" class="needs-validation" novalidate>
                      <div class="mb-3">
                        <label for="fecha_cierre" class="form-label">Fecha de Cierre</label>
                        <input type="date" class="form-control" id="fecha_cierre" name="fecha_cierre" value="<?php echo date('Y-m-d'); ?>" required>
                        <div class="invalid-feedback">
                          Por favor seleccione una fecha.
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="hora_cierre" class="form-label">Hora de Cierre</label>
                        <input type="time" class="form-control" id="hora_cierre" name="hora_cierre" value="<?php echo date('H:i'); ?>" required>
                        <div class="invalid-feedback">
                          Por favor ingrese la hora.
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="operador" class="form-label">Operador</label>
                        <input type="text" class="form-control" id="operador" name="operador" value="<?php echo isset($_SESSION['datos_login']) ? $_SESSION['datos_login']['nombre'] : ''; ?>" readonly>
                      </div>
                      <div class="alert alert-info" role="alert">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Nota:</strong> El reporte solo incluirá los tickets que no han sido reportados previamente.
                      </div>
                      <button type="submit" class="btn btn-primary mb-1" id="btnGenerarReporte">Generar Reporte</button>
                    </form>
                  </div>
                  <div class="col-md-6">
                    <div class="card">
                      <div class="card-header">
                        <h5>Reportes Generados Hoy</h5>
                      </div>
                      <div class="card-body">
                        <div id="reportesHoy">
                          <div class="table-responsive">
                            <table class="table table-striped">
                              <thead>
                                <tr>
                                  <th>Fecha</th>
                                  <th>Hora</th>
                                  <th>Operador</th>
                                  <th>Acciones</th>
                                </tr>
                              </thead>
                              <tbody id="tablaReportesHoy">
                                <!-- Aquí se cargarán los reportes generados hoy -->
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-5">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          <h5>Historial de Reportes</h5>
                        </div>
                        <div class="card-body">
                          <div class="mb-3">
                            <div class="row">
                              <div class="col-md-4">
                                <div class="input-group">
                                  <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                  <input type="date" class="form-control" id="fecha_filtro_historial" placeholder="Filtrar por fecha">
                                </div>
                              </div>
                              <div class="col-md-4">
                                <button type="button" class="btn btn-primary" id="btnFiltrarHistorial">
                                  <i class="fas fa-search"></i> Filtrar
                                </button>
                                <button type="button" class="btn btn-secondary ms-2" id="btnLimpiarFiltroHistorial" style="display: none;">
                                  <i class="fas fa-times"></i> Limpiar
                                </button>
                              </div>
                            </div>
                          </div>
                          <div class="table-responsive">
                            <table class="table table-striped">
                              <thead>
                                <tr>
                                  <th>Fecha</th>
                                  <th>Hora</th>
                                  <th>Total Recaudado</th>
                                  <th>Operador</th>
                                  <th>Acciones</th>
                                </tr>
                              </thead>
                              <tbody id="tablaHistorialReportes">
                                <!-- Aquí se cargarán los reportes históricos -->
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->
  <!-- [ Footer ] start -->
  <?php include 'layouts/footer.php'; ?>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const tabs = document.querySelectorAll(".nav-link");
      const contents = document.querySelectorAll(".tab-content");

      tabs.forEach(tab => {
        tab.addEventListener("click", function(event) {
          event.preventDefault();

          // Quitar la clase "active" de todas las pestañas
          tabs.forEach(t => t.classList.remove("active"));
          // Ocultar todos los contenidos
          contents.forEach(content => content.classList.add("d-none"));

          // Agregar "active" a la pestaña seleccionada
          this.classList.add("active");
          // Mostrar el contenido correspondiente
          document.getElementById(this.getAttribute("data-tab")).classList.remove("d-none");
        });
      });
      
      // Verificar si hay un parámetro de tab en la URL
      const urlParams = new URLSearchParams(window.location.search);
      const tabParam = urlParams.get('tab');
      if (tabParam) {
        // Activar la pestaña correspondiente
        const tabToActivate = document.querySelector(`.nav-link[data-tab="${tabParam}"]`);
        if (tabToActivate) {
          tabToActivate.click();
        }
      }
      
      // Botón para limpiar filtros en la sección de recibos
      const btnLimpiarFiltros = document.getElementById('limpiarFiltros');
      if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', function() {
          // Redirigir a la página sin filtros pero manteniendo la pestaña activa
          window.location.href = '?tab=tab2';
        });
      }
      
      // Botones para ver detalles de recibos
      const botonesVerRecibo = document.querySelectorAll('.ver-recibo');
      if (botonesVerRecibo.length > 0) {
        botonesVerRecibo.forEach(boton => {
          boton.addEventListener('click', function() {
            const idRecibo = this.getAttribute('data-id');
            const modal = new bootstrap.Modal(document.getElementById('modalDetalleRecibo'));
            
            // Mostrar el modal con el spinner de carga
            modal.show();
            
            // Cargar los detalles del recibo mediante AJAX
            fetch(`../../controladores/obtener_detalle_recibo.php?id=${idRecibo}`)
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  // Formatear los datos para mostrarlos en el modal
                  let contenidoHTML = `
                    <div class="recibo-detalle">
                      <div class="text-center mb-4">
                        <h4>Comprobante de Pago</h4>
                        <p>SmartPark - Sistema de Parqueadero</p>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <p><strong>Recibo #:</strong> ${data.recibo.id_registro}</p>
                          <p><strong>Fecha de Ingreso:</strong> ${data.recibo.hora_ingreso_formateada}</p>
                          <p><strong>Fecha de Salida:</strong> ${data.recibo.hora_salida_formateada}</p>
                          <p><strong>Tiempo de Estancia:</strong> ${data.recibo.tiempo_estancia}</p>
                        </div>
                        <div class="col-md-6">
                          <p><strong>Placa:</strong> ${data.recibo.placa}</p>
                          <p><strong>Tipo de Vehículo:</strong> ${data.recibo.tipo_vehiculo}</p>
                          <p><strong>Método de Pago:</strong> ${data.recibo.metodo_pago}</p>
                        </div>
                      </div>
                      <hr>
                      <div class="card mb-3">
                        <div class="card-header bg-info-subtle py-2">
                          <h6 class="mb-0">Detalle de factura</h6>
                        </div>
                        <div class="card-body p-3">
                          <div class="row mb-2">
                            <div class="col-6"><strong>Estacionamiento</strong></div>
                            <div class="col-6 text-end">${data.recibo.costo_estacionamiento_formateado}</div>
                          </div>
                          ${data.recibo.costos_adicionales && data.recibo.costos_adicionales.length > 0 ? 
                            data.recibo.costos_adicionales.map(costo => `
                              <div class="row mb-2">
                                <div class="col-6">${costo.concepto}</div>
                                <div class="col-6 text-end">${costo.valor_formateado}</div>
                              </div>
                            `).join('') : ''}
                          <hr class="my-2">
                          <div class="row">
                            <div class="col-6"><strong>TOTAL</strong></div>
                            <div class="col-6 text-end fw-bold">$${Number(data.recibo.total_pagado).toLocaleString('es-CO')}</div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-12">
                          <p><strong>Descripción:</strong> ${data.recibo.descripcion || 'Sin descripción'}</p>
                          <p><strong>Operador que abrió:</strong> ${data.recibo.abierto_por}</p>
                          <p><strong>Operador que cerró:</strong> ${data.recibo.cerrado_por}</p>
                        </div>
                      </div>
                    </div>
                  `;
                  
                  // Actualizar el contenido del modal
                  document.getElementById('contenidoDetalleRecibo').innerHTML = contenidoHTML;
                  
                  // Configurar el botón de imprimir
                  const btnImprimir = document.getElementById('btnImprimirReciboModal');
                  btnImprimir.setAttribute('data-id', idRecibo);
                  btnImprimir.addEventListener('click', function() {
                    const idRecibo = this.getAttribute('data-id');
                    window.open(`../../controladores/imprimir_recibo.php?id=${idRecibo}`, '_blank');
                  });
                } else {
                  // Mostrar mensaje de error
                  document.getElementById('contenidoDetalleRecibo').innerHTML = `
                    <div class="alert alert-danger">
                      <p>Error al cargar los detalles del recibo: ${data.message}</p>
                    </div>
                  `;
                }
              })
              .catch(error => {
                console.error('Error:', error);
                document.getElementById('contenidoDetalleRecibo').innerHTML = `
                  <div class="alert alert-danger">
                    <p>Error al cargar los detalles del recibo. Por favor, inténtelo de nuevo.</p>
                  </div>
                `;
              });
          });
        });
      }
      
      // Botones para imprimir recibos directamente desde la tabla
      const botonesImprimirRecibo = document.querySelectorAll('.imprimir-recibo');
      if (botonesImprimirRecibo.length > 0) {
        botonesImprimirRecibo.forEach(boton => {
          boton.addEventListener('click', function() {
            const idRecibo = this.getAttribute('data-id');
            window.open(`../../controladores/imprimir_recibo.php?id=${idRecibo}`, '_blank');
          });
        });
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- [Page Specific JS] end -->
  <!-- Required Js -->
  <script src="../assets/js/plugins/popper.min.js"></script>
  <script src="../assets/js/plugins/simplebar.min.js"></script>
  <script src="../assets/js/plugins/bootstrap.min.js"></script>
  <script src="../assets/js/fonts/custom-font.js"></script>
  <script src="../assets/js/pcoded.js"></script>
  <script src="../assets/js/plugins/feather.min.js"></script>
  <script>
    layout_change('light');
  </script>
  <script>
    change_box_container('false');
  </script>
  <script>
    layout_rtl_change('false');
  </script>
  <script>
    preset_change("preset-1");
  </script>
  <script>
    font_change("Public-Sans");
  </script>
  
  <!-- Script para el cierre de caja -->
  <script src="../assets/js/cierre_caja.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Manejar el formulario de búsqueda de recibos
      const formBusqueda = document.getElementById('formBusquedaRecibos');
      const fechaBusqueda = document.getElementById('fecha_busqueda');
      const placaBusqueda = document.querySelector('input[name="placa_busqueda"]');
      const tipoBusqueda = document.querySelector('select[name="tipo_busqueda"]');
      const metodoPagoBusqueda = document.querySelector('select[name="metodo_pago_busqueda"]');
      
      if (formBusqueda) {
        // Asegurarse de que el campo de fecha esté vacío al cargar la página
        fechaBusqueda.value = '';
        
        formBusqueda.addEventListener('submit', function(e) {
          // No establecer la fecha automáticamente en ningún caso
          // Permitir que el usuario busque con cualquier combinación de filtros
          // sin modificar el valor del campo fecha
        });

        // Manejar el botón de limpiar filtros
        const btnLimpiar = document.getElementById('limpiarFiltros');
        if (btnLimpiar) {
          btnLimpiar.addEventListener('click', function() {
            // Limpiar todos los campos del formulario
            fechaBusqueda.value = '';
            placaBusqueda.value = '';
            tipoBusqueda.value = '';
            metodoPagoBusqueda.value = '';
            
            // Enviar el formulario para recargar la página sin filtros
            formBusqueda.submit();
          });
        }
      }
    });
  </script>
  <script>
    // Función para formatear moneda
    function formatearMoneda(valor) {
      return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
      }).format(valor);
    }

    // Función para formatear tiempo
    function formatearTiempo(horas, minutos) {
      if (horas < 1) {
        if (minutos === 0) {
          return 'Menos de 1m';
        }
        return `${minutos}m`;
      }
      return `${Math.floor(horas)}h ${minutos}m`;
    }

    // Función para cargar los tickets activos
    function cargarTicketsActivos() {
      $.ajax({
        url: '../../controladores/caja.php',
        type: 'POST',
        data: { accion: 'obtener_tickets' },
        success: function(response) {
          const tickets = JSON.parse(response);
          let html = '';
          
          tickets.forEach(ticket => {
            html += `
              <tr>
                <td>${ticket.id_registro}</td>
                <td>${ticket.placa}</td>
                <td>${ticket.tipo_vehiculo}</td>
                <td>${ticket.tipo_tiempo}</td>
                <td>${ticket.nombre_cliente || 'N/A'}</td>
                <td>${new Date(ticket.hora_ingreso).toLocaleString()}</td>
                <td>${formatearTiempo(ticket.horas_transcurridas, ticket.minutos_transcurridos)}</td>
                <td>
                  <button class="btn btn-sm btn-info" onclick="verDetalleTicket(${ticket.id_registro})">
                    <i class="fas fa-eye"></i>
                  </button>
                </td>
              </tr>
            `;
          });
          
          $('#lista-tickets').html(html);
        }
      });
    }

    // Función para cargar el resumen de caja
    function cargarResumenCaja() {
      $.ajax({
        url: '../../controladores/caja.php',
        type: 'POST',
        data: { accion: 'obtener_resumen' },
        success: function(response) {
          const resumen = JSON.parse(response);
          $('#total-tickets').text(resumen.total_tickets);
          $('#total-efectivo').text(formatearMoneda(resumen.total_efectivo));
          $('#total-tarjeta').text(formatearMoneda(resumen.total_tarjeta));
          $('#total-transferencia').text(formatearMoneda(resumen.total_transferencia));
        }
      });
    }

    // Función para actualizar método de pago
    function actualizarMetodoPago(select) {
      const idRegistro = $(select).data('id');
      const metodoPago = $(select).val();
      
      $.ajax({
        url: '../../controladores/caja.php',
        type: 'POST',
        data: {
          accion: 'actualizar_metodo_pago',
          id_registro: idRegistro,
          metodo_pago: metodoPago
        },
        success: function(response) {
          const result = JSON.parse(response);
          if (result.success) {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'success',
              title: 'Método de pago actualizado correctamente',
              showConfirmButton: false,
              timer: 3000
            });
            cargarResumenCaja();
          }
        }
      });
    }

    // Función para ver detalle del ticket
    function verDetalleTicket(idRegistro) {
      $.ajax({
        url: '../../controladores/caja.php',
        type: 'POST',
        data: {
          accion: 'obtener_detalle_ticket',
          id_registro: idRegistro
        },
        success: function(response) {
          const ticket = JSON.parse(response);
          Swal.fire({
            title: 'Detalle del Ticket',
            html: `
              <div class="text-start">
                <p><strong>ID:</strong> ${ticket.id_registro}</p>
                <p><strong>Placa:</strong> ${ticket.placa}</p>
                <p><strong>Tipo Vehículo:</strong> ${ticket.tipo_vehiculo}</p>
                <p><strong>Tipo Tiempo:</strong> ${ticket.tipo_tiempo}</p>
                <p><strong>Cliente:</strong> ${ticket.nombre_cliente || 'N/A'}</p>
                <p><strong>Ingreso:</strong> ${new Date(ticket.hora_ingreso).toLocaleString()}</p>
                <p><strong>Tiempo:</strong> ${formatearTiempo(ticket.horas_transcurridas, ticket.minutos_transcurridos)}</p>
              </div>
            `,
            icon: 'info'
          });
        }
      });
    }

    // Cargar datos iniciales
    $(document).ready(function() {
      cargarTicketsActivos();
      cargarResumenCaja();
      
      // Actualizar cada minuto
      setInterval(function() {
        cargarTicketsActivos();
        cargarResumenCaja();
      }, 60000);
    });
  </script>
</body>
<!-- [Body] end -->

</html>