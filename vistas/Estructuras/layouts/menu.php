<nav class="pc-sidebar">
    <div class="navbar-wrapper">
      <!-- Activador del menú -->
      <div class="m-header">
        <a href="gestion.php" class="b-brand text-primary">
          <img src="../assets/images/logo2.jpeg" class="img-fluid" alt="logo">
        </a>
      </div>

      <div class="navbar-content">
        <ul class="pc-navbar">
          <li class="pc-item">
            <a href="gestion.php" class="pc-link">
              <span class="pc-micon"><i class="ti ti-home"></i></span>
              <span class="pc-mtext">Inicio</span>
            </a>
          </li>

          <li class="pc-item pc-caption">
            <label>Páginas</label>
          </li>

          <li class="pc-item">
            <a href="clientes.php" class="pc-link">
              <span class="pc-micon"><i class="ti ti-user"></i></span>
              <span class="pc-mtext">Clientes</span>
            </a>
          </li>

          <li class="pc-item">
            <a href="caja.php" class="pc-link">
              <span class="pc-micon"><i class="ti ti-credit-card"></i></span>
              <span class="pc-mtext">Caja</span>
            </a>
          </li>

          <li class="pc-item">
            <a href="incidentes.php" class="pc-link">
              <span class="pc-micon"><i class="ti ti-alert-triangle"></i></span>
              <span class="pc-mtext">Incidentes</span>
            </a>
          </li>

          <?php if(isset($_SESSION['datos_login']) && $_SESSION['datos_login']['rol'] == 'administrador'): ?>
          <li class="pc-item">
            <a href="configuracion.php" class="pc-link">
              <span class="pc-micon"><i class="ti ti-settings"></i></span>
              <span class="pc-mtext">Configuración</span>
            </a>
          </li>
          <?php endif; ?>

          <li class="pc-item pc-caption">
            <label>Otros</label>
          </li>

          <li class="pc-item">
            <a href="../../controladores/cerrar_sesion.php" class="pc-link">
              <span class="pc-micon"><i class="ti ti-logout"></i></span>
              <span class="pc-mtext">Salir</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>