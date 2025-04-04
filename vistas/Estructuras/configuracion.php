<?php
session_start();
include '../../modelo/conexion.php';
include '../../controladores/seguridad.php';

// Verificar si el usuario es administrador
if(!isset($_SESSION['datos_login']) || $_SESSION['datos_login']['rol'] != 'administrador') {
    // Redirigir al usuario a la página principal si no es administrador
    header("location: gestion.php");
    exit();
}

include '../../controladores/consultas_configuracion_tap1.php';
?>
<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>Home | SmartPark</title>
  <!-- [Meta] Información meta para el documento -->
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
    <!-- Incluir SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- [Bootstrap CSS] -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- [Bootstrap Icons] -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <!-- [Tabler Icons] -->
  <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
  <!-- [Feather Icons] -->
  <link rel="stylesheet" href="../assets/fonts/feather.css">
  <!-- [Font Awesome Icons] -->
  <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
  <!-- [Material Icons] -->
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
            <a class="nav-link active" data-tab="tab1" href="#">
              <i class="bi bi-grid"></i> Categorías
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab2" href="#">
              <i class="bi bi-tags"></i> Tarifas
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab3" href="#">
              <i class="bi bi-cash-stack"></i> Precios
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab4" href="#">
              <i class="bi bi-people"></i> Usuarios
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab5" href="#">
              <i class="bi bi-credit-card"></i> Medios de pago
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab6" href="#">
              <i class="bi bi-clipboard-data"></i> Informes
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab7" href="#">
              <i class="bi bi-gear"></i> Ajustes
            </a>
          </li>
        </ul>

        <!-- Contenido dinámico -->
        <div class="tab-container mt-4">
          <!-- Tab de Categorías -->
          <?php include 'configuracion_tap/tap1.php'; ?>

          <!-- Tab de Tarifas -->
          <?php include 'configuracion_tap/tap2.php'; ?>
          <!-- Tab de Precios -->
          <?php include 'configuracion_tap/tap3.php'; ?>
          <!-- Tab de Usuarios -->
          <?php include 'configuracion_tap/tap4.php'; ?>
          <!-- Tab de Medios de Pago -->
          <div id="tab5" class="tab-content d-none">
            <h3>Medios de pago</h3>
            <p>Sección de análisis y estadísticas sobre el rendimiento del sistema.</p>
          </div>
          <!-- Tab de Informes -->
          <div id="tab6" class="tab-content d-none">
            <h3>Informes</h3>
            <p>Sección de análisis y estadísticas sobre el rendimiento del sistema.</p>
          </div>
          <!-- Tab de Ajustes -->
          <div id="tab7" class="tab-content d-none">
            <h3>Ajustes</h3>
            <p class="text-muted small">Establece los datos de la empresa y las impresoras</p>
            <div class="card p-4 mb-4">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Empresa</label>
                    <input type="text" class="form-control" placeholder="Nombre">
                    <input type="text" class="form-control mt-2" placeholder="Dirección">
                    <input type="text" class="form-control mt-2" placeholder="Ciudad">
                    <input type="text" class="form-control mt-2" placeholder="País">
                    <input type="text" class="form-control mt-2" placeholder="Teléfono">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Impresoras</label>
                    <select class="form-select">
                      <option>Microsoft Print to PDF</option>
                    </select>
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
    let categoriaSeleccionada = null;

    function seleccionarCategoria(elemento) {
      // Remover selección previa
      document.querySelectorAll('#categoriasList .list-group-item').forEach(item => {
        item.classList.remove('active');
      });

      // Agregar selección al elemento clickeado
      elemento.classList.add('active');
      categoriaSeleccionada = elemento.textContent;

      // Habilitar botones
      document.getElementById('btnEliminar').disabled = false;
      document.getElementById('btnModificar').disabled = false;

      // Actualizar el input del modal modificar
      document.getElementById('nombreCategoria').value = categoriaSeleccionada;

      // Actualizar el texto del modal eliminar
      document.getElementById('categoriaAEliminar').textContent = `Categoría seleccionada: ${categoriaSeleccionada}`;
    }
  </script>
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
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