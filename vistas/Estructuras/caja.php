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
            <h3>Caja</h3>
            <p>Esta sección muestra todos los tickets abiertos actualmente.</p>
          </div>
          <div id="tab2" class="tab-content d-none">
            <h3>Recibos</h3>
            <p>Aquí encontrarás los tickets que ya han sido resueltos y cerrados.</p>
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
                          Por favor seleccione una hora.
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="operador" class="form-label">Operador</label>
                        <input type="text" class="form-control" id="operador" name="operador" value="<?php echo isset($_SESSION['datos_login']) ? $_SESSION['datos_login']['nombre'] : ''; ?>" readonly>
                      </div>
                      <button type="submit" class="btn btn-primary" id="btnGenerarReporte">Generar Reporte</button>
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
                </div>
                
                <div class="row">
                  <div class="col-12">
                    <div class="card">
                      <div class="card-header">
                        <h5>Historial de Reportes</h5>
                      </div>
                      <div class="card-body">
                        <div class="mb-3">
                          <label for="fecha_busqueda" class="form-label">Buscar por fecha</label>
                          <div class="input-group">
                            <input type="date" class="form-control" id="fecha_busqueda" name="fecha_busqueda">
                            <button class="btn btn-outline-secondary" type="button" id="btnBuscarReportes">Buscar</button>
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
</body>
<!-- [Body] end -->

</html>