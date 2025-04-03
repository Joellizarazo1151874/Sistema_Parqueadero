<?php
session_start();
include '../../modelo/conexion.php';
include '../../controladores/seguridad.php';
include '../../controladores/consultas_tap1.php';
include '../../controladores/consultas_tap2.php';
include '../../controladores/consultas_tap3.php';
date_default_timezone_set('America/Bogota'); // Cambia 'America/Bogota' por tu zona horaria



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
  <!-- [Bootstrap CSS] -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- [Font Awesome CSS] -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
      <div class="mt-1">
        <!-- Menú de Navegación -->
        <ul class="nav nav-tabs justify-content-center" id="menu-tabs">
          <li class="nav-item">
            <a class="nav-link <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'tab1') ? 'active' : ''; ?>" data-tab="tab1" href="?tab=tab1">Tickets Abiertos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'tab2') ? 'active' : ''; ?>" data-tab="tab2" href="?tab=tab2">Tickets Cerrados</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'tab3') ? 'active' : ''; ?>" data-tab="tab3" href="?tab=tab3">Entradas / Salidas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'tab4') ? 'active' : ''; ?>" data-tab="tab4" href="?tab=tab4">Reportes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'tab5') ? 'active' : ''; ?>" data-tab="tab5" href="?tab=tab5">Estadísticas</a>
          </li>
        </ul>

        <!-- Contenido dinámico -->
        <div class="tab-container mt-4">
          <!-- tap 1 tickets abiertos -->
          <?php include 'gestion_tap/tap1.php'; ?>
          <!-- tap 2 tickets cerrados -->
          <?php include 'gestion_tap/tap2.php'; ?>
          <!-- tap 2 tickets cerrados -->
          <?php include 'gestion_tap/tap3.php'; ?>

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
  // Activar la pestaña correspondiente según la URL
  document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab') || 'tab1';
    
    // Mostrar la pestaña activa
    document.querySelectorAll('.tab-content').forEach(function(content) {
      content.classList.add('d-none');
    });
    
    const activeTab = document.getElementById(tab);
    if (activeTab) {
      activeTab.classList.remove('d-none');
    }
  });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- [Page Specific JS] end -->
  <!-- Required Js -->
  <script src="../assets/js/plugins/popper.min.js"></script>
  <script src="../assets/js/plugins/simplebar.min.js"></script>
  <script src="../assets/js/plugins/bootstrap.min.js"></script>
  <script src="../assets/js/fonts/custom-font.js"></script>
  <script src="../assets/js/pcoded.js"></script>
  <script src="../assets/js/plugins/feather.min.js"></script>
  <script src="../assets/js/ticket.js"></script>
  <script>
    // Script para cargar tarifas y tolerancias actualizadas
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Cargando valores de tarifas y tolerancias actualizados...');
      
      // Verificar si estamos en la pestaña de tickets abiertos (tab1)
      const currentTab = new URLSearchParams(window.location.search).get('tab') || 'tab1';
      if (currentTab !== 'tab1') return;
      
      // Cargar las tolerancias primero
      fetch('../../controladores/obtener_tolerancia.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            console.log('Tolerancias cargadas:', data.tolerancias);
            // No necesitamos hacer nada más porque las tolerancias ya se cargan en PHP
          } else {
            console.error('Error al cargar tolerancias:', data.error);
          }
        })
        .catch(error => {
          console.error('Error de red al cargar tolerancias:', error);
        });
    });
  </script>
</body>
<!-- [Body] end -->

</html>