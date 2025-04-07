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
          <li class="nav-item">
            <a class="nav-link" data-tab="tab8" href="#">
              <i class="bi bi-camera"></i> Cámara
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
          <?php include 'configuracion_tap/tap5.php'; ?>

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

          <!-- Tab de Cámara -->
          <div id="tab8" class="tab-content d-none">
            <h3>Configuración de Cámara</h3>
            <p class="text-muted small">Configure la cámara para el reconocimiento automático de placas</p>
            <div class="card p-4 mb-4">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Seleccionar Dispositivo</label>
                    <select class="form-select" id="cameraSelect">
                      <option value="">Seleccione una cámara...</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Resolución</label>
                    <select class="form-select" id="resolutionSelect">
                      <option value="640x480">640x480</option>
                      <option value="1280x720">1280x720</option>
                      <option value="1920x1080">1920x1080</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Intervalo de Reconocimiento (segundos)</label>
                    <input type="number" class="form-control" id="scanInterval" min="1" max="10" value="3">
                  </div>
                  <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="autoStartCamera" checked>
                    <label class="form-check-label" for="autoStartCamera">Iniciar cámara automáticamente</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Vista Previa</label>
                    <div class="border rounded p-2 d-flex justify-content-center align-items-center bg-light" style="height: 240px;">
                      <div id="cameraPreview" class="text-center">
                        <i class="bi bi-camera-video-off" style="font-size: 48px;"></i>
                        <p>La cámara no está activa</p>
                      </div>
                    </div>
                  </div>
                  <div class="d-grid gap-2">
                    <button class="btn btn-primary" id="startCameraBtn">
                      <i class="bi bi-camera-video"></i> Iniciar Cámara
                    </button>
                    <button class="btn btn-danger" id="stopCameraBtn" disabled>
                      <i class="bi bi-camera-video-off"></i> Detener Cámara
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card p-4 mb-4">
              <h4>Configuración de Reconocimiento</h4>
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Sensibilidad de Detección</label>
                    <input type="range" class="form-range" id="sensitivityRange" min="1" max="10" value="5">
                    <div class="d-flex justify-content-between">
                      <span>Baja</span>
                      <span>Media</span>
                      <span>Alta</span>
                    </div>
                  </div>
                  <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="enableNotifications" checked>
                    <label class="form-check-label" for="enableNotifications">Habilitar notificaciones sonoras</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Última Placa Detectada</label>
                    <div class="d-flex">
                      <input type="text" class="form-control" id="lastDetectedPlate" readonly value="" placeholder="Ninguna placa detectada">
                      <button class="btn btn-outline-secondary ms-2" id="testRecognitionBtn" title="Probar reconocimiento">
                        <i class="bi bi-play-fill"></i>
                      </button>
                    </div>
                  </div>
                  <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> El sistema de reconocimiento de placas funciona mejor con buena iluminación y placas claramente visibles.
                  </div>
                </div>
              </div>
              <div class="d-grid gap-2 col-md-4 mx-auto mt-3">
                <button class="btn btn-success" id="saveSettingsBtn">
                  <i class="bi bi-save"></i> Guardar Configuración
                </button>
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
  <!-- El script de cámara persistente ya está cargado en el header -->
  <script>
    // Asegurarse de que la página de configuración interactúe con el controlador persistente
    document.addEventListener('DOMContentLoaded', function() {
      // Esperar a que el controlador persistente esté disponible
      const checkController = setInterval(() => {
        if (window.persistentCameraController) {
          clearInterval(checkController);
          console.log('Controlador de cámara persistente detectado en la página de configuración');
        }
      }, 100);
    });
  </script>
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