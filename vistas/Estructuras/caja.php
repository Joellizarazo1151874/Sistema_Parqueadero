<?php
session_start();
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
            <h3>Cierre de caja</h3>
            <p>Registro de entradas y salidas de los usuarios en el sistema.</p>
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
</body>
<!-- [Body] end -->

</html>