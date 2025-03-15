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
            <a class="nav-link active" data-tab="tab1" href="#">Clientes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab2" href="#">Vehículos</a>
          </li>
        </ul>

        <!-- Contenido dinámico -->
        <div class="tab-container mt-4">
          <div id="tab1" class="tab-content active">
            <div class="row">
              <!-- Columna principal -->
              <div class="col-md-12">
                <div class="ticket-summary card p-3">
                  <!-- Filtros -->
                  <div class="d-flex align-items-center gap-2 mb-3">
                    <input type="text" class="form-control" placeholder="Matrícula">
                    <input type="text" class="form-control" placeholder="Cliente">

                    <button class="btn btn-outline-secondary">
                      <i class="fas fa-share-alt"></i>
                    </button>
                  </div>

                  <!-- Tabla de tickets -->
                  <div class="ticket-summary">
                    <div class="table-responsive">
                      <table class="table custom-table">
                        <thead>
                          <tr class="highlight-column">
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Vehículos</th>
                            <th>Matriculas</th>
                            <th>Valance</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1</td>
                            <td>JUAN CASTILLO</td>
                            <td>2</td>
                            <td>ABC-123,BEC-231</td>
                            <td>12.000</td>
                          </tr>
                          <tr>
                            <td>2</td>
                            <td>CARLOS CASTILLO</td>
                            <td>1</td>
                            <td>BEC-231</td>
                            <td>12.000</td>
                          </tr>
                          <tr>
                            <td>3</td>
                            <td>SAMANTA CASTILLO</td>
                            <td>1</td>
                            <td>AED-231</td>
                            <td>12.000</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="text-end mt-3">
                    <button class="btn btn-primary">Crear Cliente</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
</body>
<!-- [Body] end -->

</html>