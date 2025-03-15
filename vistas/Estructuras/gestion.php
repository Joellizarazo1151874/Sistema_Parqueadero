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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
      <div class="mt-1">
        <!-- Menú de Navegación -->
        <ul class="nav nav-tabs justify-content-center" id="menu-tabs">
          <li class="nav-item">
            <a class="nav-link active" data-tab="tab1" href="#">Tickets Abiertos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab2" href="#">Tickets Cerrados</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab3" href="#">Entradas / Salidas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab4" href="#">Reportes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab5" href="#">Estadísticas</a>
          </li>
        </ul>

        <!-- Contenido dinámico -->
        <div class="tab-container mt-4">
          <div id="tab1" class="tab-content active">
            <div class="tickets-container">
              <div class="ticket">
                <!-- Cuadro con el ícono del reloj -->
                <div class="row" style="background-color: rgb(174, 213, 255); padding: 15px; ">
                  <div class="col-3">
                    <div class="time-box">
                      <i class="feather icon-clock"></i>
                      <span>HORA</span>
                    </div>
                  </div>
                  <div class="col-9">
                    <div class="ticket-header">
                      <h3>CAMIONETA</h3>
                      <p class="plate">ABC-123</p>
                    </div>
                  </div>
                </div>
                <div class="ticket-content">
                  <p>Entrada: <span>13/03 09:26</span></p>
                  <p>Tiempo: <span>0:00</span></p>
                  <p>Importe Actual: <span>$100</span></p>
                  <p>Debe: <span class="debt">$100</span></p>
                </div>

                <div class="ticket-actions">
                  <div class="icon-btn-group">
                    <button class="icon-btn"><i class="fas fa-edit"></i></button>
                    <button class="icon-btn"><i class="fas fa-print"></i></button>
                    <button class="icon-btn"><i class="fas fa-file-alt"></i></button>
                  </div>
                  <button class="close-btn">Cerrar</button>
                </div>
              </div>
              <div class="ticket">
                <!-- Cuadro con el ícono del reloj -->
                <div class="row" style="background-color: rgb(174, 213, 255); padding: 15px; ">
                  <div class="col-3">
                    <div class="time-box">
                      <i class="feather icon-clock"></i>
                      <span>HORA</span>
                    </div>
                  </div>
                  <div class="col-9">
                    <div class="ticket-header">
                      <h3>CAMIONETA</h3>
                      <p class="plate">ABC-123</p>
                    </div>
                  </div>
                </div>
                <div class="ticket-content">
                  <p>Entrada: <span>13/03 09:26</span></p>
                  <p>Tiempo: <span>0:00</span></p>
                  <p>Importe Actual: <span>$100</span></p>
                  <p>Debe: <span class="debt">$100</span></p>
                </div>

                <div class="ticket-actions">
                  <div class="icon-btn-group">
                    <button class="icon-btn"><i class="fas fa-edit"></i></button>
                    <button class="icon-btn"><i class="fas fa-print"></i></button>
                    <button class="icon-btn"><i class="fas fa-file-alt"></i></button>
                  </div>
                  <button class="close-btn">Cerrar</button>
                </div>
              </div>
              <div class="ticket">
                <!-- Cuadro con el ícono del reloj -->
                <div class="row" style="background-color: rgb(174, 213, 255); padding: 15px; ">
                  <div class="col-3">
                    <div class="time-box">
                      <i class="feather icon-clock"></i>
                      <span>HORA</span>
                    </div>
                  </div>
                  <div class="col-9">
                    <div class="ticket-header">
                      <h3>CAMIONETA</h3>
                      <p class="plate">ABC-123</p>
                    </div>
                  </div>
                </div>
                <div class="ticket-content">
                  <p>Entrada: <span>13/03 09:26</span></p>
                  <p>Tiempo: <span>0:00</span></p>
                  <p>Importe Actual: <span>$100</span></p>
                  <p>Debe: <span class="debt">$100</span></p>
                </div>

                <div class="ticket-actions">
                  <div class="icon-btn-group">
                    <button class="icon-btn"><i class="fas fa-edit"></i></button>
                    <button class="icon-btn"><i class="fas fa-print"></i></button>
                    <button class="icon-btn"><i class="fas fa-file-alt"></i></button>
                  </div>
                  <button class="close-btn">Cerrar</button>
                </div>
              </div>

            </div>
          </div>
          <div id="tab2" class="tab-content d-none">
            <div class="row">
              <div class="col-lg-3 col-md-4">
                <div class="filters card p-3">
                  <h6 class="fw-bold">Filtros</h6>
                  <div class="filter-options">
                    <div class="mb-2">
                      <label class="form-label">Categoría</label>
                      <select class="form-select">
                        <option value="todos">Todas</option>
                      </select>
                    </div>
                    <div class="mb-2">
                      <label class="form-label">Abierto por</label>
                      <select class="form-select">
                        <option value="todos">Todos los Operadores</option>
                      </select>
                    </div>
                    <div class="mb-2">
                      <label class="form-label">Cerrado por</label>
                      <select class="form-select">
                        <option value="todos">Todos los Operadores</option>
                      </select>
                    </div>
                    <div class="mb-2">
                      <label class="form-label">Tipo de Tickets</label>
                      <select class="form-select">
                        <option value="todos">Todos</option>
                      </select>
                    </div>
                    <div class="mb-2">
                      <label class="form-label">Cancelación</label>
                      <select class="form-select">
                        <option value="todos">Todos</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-9 col-md-8">
                <div class="ticket-summary">
                  <h3>Tickets Cerrados</h3>
                  <p>Aquí encontrarás los tickets que ya han sido resueltos y cerrados.</p>
                  <div class="table-responsive">
                    <table class="table custom-table">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Fecha</th>
                          <th>Matricula</th>
                          <th>Categoría</th>
                          <th>Tipo</th>
                          <th>Desde</th>
                          <th>Hasta</th>
                          <th>Observación</th>
                          <th>Importe</th>
                          <th>Pagado</th>
                          <th>Abierto por</th>
                          <th>Cerrado por</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>101</td>
                          <td>13/03/25 09:26</td>
                          <td>JOEL</td>
                          <td>MOTO</td>
                          <td>x Hora</td>
                          <td>09:26 (13/03)</td>
                          <td>09:26 (13/03)</td>
                          <td>Observación</td>
                          <td>$100</td>
                          <td>$100</td>
                          <td>JOEL LIZARAZO</td>
                          <td>JOEL LIZARAZO</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="summary-stats row">
                  <div class="stat col-6 col-md-3">
                    <strong>Tickets Cerrados</strong>
                    <span>1</span>
                  </div>
                  <div class="stat col-6 col-md-3">
                    <strong>Cancelados</strong>
                    <span>0</span>
                  </div>
                  <div class="stat col-6 col-md-3">
                    <strong>Tickets x Hora</strong>
                    <span>1</span>
                  </div>
                  <div class="stat col-6 col-md-3">
                    <strong>Importe Total</strong>
                    <span>$100</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
                          <td>➡️</td>
                          <td>13/03/25</td>
                          <td>09:26</td>
                          <td>#JOEL</td>
                          <td>Moto</td>
                          <td>101</td>
                          <td>--</td>
                          <td>joel lizarazo</td>
                        </tr>
                        <tr>
                          <td>➡️</td>
                          <td>13/03/25</td>
                          <td>09:26</td>
                          <td>#CARRO</td>
                          <td>Auto</td>
                          <td>102</td>
                          <td>--</td>
                          <td>joel lizarazo</td>
                        </tr>
                        <tr>
                          <td>⬅️</td>
                          <td>13/03/25</td>
                          <td>09:26</td>
                          <td>#CAMIONETA</td>
                          <td>Camioneta</td>
                          <td>103</td>
                          <td>--</td>
                          <td>joel lizarazo</td>
                        </tr>
                        <tr>
                          <td>➡️</td>
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
          <div id="tab4" class="tab-content d-none">
            <h3>Reportes</h3>
            <p>Genera y visualiza reportes detallados sobre los tickets y actividades.</p>
          </div>
          <div id="tab5" class="tab-content d-none">
            <h3>Estadísticas</h3>
            <p>Sección de análisis y estadísticas sobre el rendimiento del sistema.</p>
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
  <!-- [Page Specific JS] start -->
  <script src="../assets/js/plugins/apexcharts.min.js"></script>
  <script src="../assets/js/pages/dashboard-default.js"></script>
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
    layout_rtl_change('false');
  </script>
  <script>
    preset_change("preset-1");
  </script>
  <script>
    font_change("Public-Sans");
  </script>
</body>
<!-- [Body] end -->

</html>