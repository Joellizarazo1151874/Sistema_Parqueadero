  <!-- [ Sidebar Menu ] end --> <!-- [ Header Topbar ] start -->
  <!-- SweetAlert2 CSS y JS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
  
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  
  <!-- Asegurar que Bootstrap está correctamente cargado -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Persistent Camera Controller -->
  <script src="../assets/js/camera/camera-persistent.js"></script>
  <script>
    // Inicializar todos los dropdowns de Bootstrap cuando la página cargue
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Inicializando dropdowns de Bootstrap');
      var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
      var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
      });
    });
  </script>
  
  <header class="pc-header">
    <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
      <div class="me-auto pc-mob-drp">
        <ul class="list-unstyled">
          <!-- ======= Menu collapse Icon ===== -->
          <li class="pc-h-item pc-sidebar-collapse">
            <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
              <i class="ti ti-menu-2"></i>
            </a>
          </li>
          
        </ul>
      </div>
      <!-- [Mobile Media Block end] -->
      <div class="ms-auto">
        <ul class="list-unstyled">
          <!-- Botón de cámara para reconocimiento de placas -->
          <li class="pc-h-item">
            <a href="#" class="pc-head-link" id="openCameraBtn" title="Reconocimiento de Placas">
              <i class="ti ti-camera"></i>
            </a>
          </li>
          <li class="dropdown pc-h-item header-user-profile">
            <a
              class="pc-head-link dropdown-toggle arrow-none me-0"
              data-bs-toggle="dropdown"
              href="#"
              role="button"
              aria-haspopup="false"
              data-bs-auto-close="outside"
              aria-expanded="false">
              <img src="../assets/images/user/avatar-2.jpg" alt="user-image" class="user-avtar">
              <span><?php echo $_SESSION['datos_login']['nombre']; ?></span>
            </a>
            <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
              <div class="dropdown-header">
                <div class="d-flex mb-1">
                  <div class="flex-shrink-0">
                    <img src="../assets/images/user/avatar-2.jpg" alt="user-image" class="user-avtar wid-35">
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1"><?php echo $_SESSION['datos_login']['nombre']; ?></h6>
                    <span><?php echo $_SESSION['datos_login']['rol']; ?></span>
                  </div>
                  <a href="../../controladores/cerrar_sesion.php" class="pc-head-link bg-transparent"><i class="ti ti-power text-danger"></i></a>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </header>
  <!-- [ Header ] end -->
  
  <!-- Persistent Camera Container - This stays mounted across all pages -->
  <div id="persistentCameraContainer" class="persistent-camera-container" style="display: none; position: fixed; z-index: 9999;"></div>