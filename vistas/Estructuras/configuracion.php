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
          <div id="tab1" class="tab-content active">
            <div class="row justify-content-center">
              <div class="col-md-5">
                <div class="card p-3">
                  <h3 class="text-center mb-4">Categorías</h3>
                  <div class="list-group mb-3" id="categoriasList">
                    <div class="list-group-item" onclick="seleccionarCategoria(this)">Moto</div>
                    <div class="list-group-item" onclick="seleccionarCategoria(this)">Auto</div>
                    <div class="list-group-item" onclick="seleccionarCategoria(this)">Camioneta</div>
                  </div>
                  <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-outline-danger" id="btnEliminar" data-bs-toggle="modal" data-bs-target="#eliminarModal" disabled>Eliminar</button>
                    <button class="btn btn-outline-primary" id="btnModificar" data-bs-toggle="modal" data-bs-target="#modificarModal" disabled>Modificar</button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevaModal">Nueva Categoría</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal Eliminar -->
            <div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="eliminarModalLabel">Eliminar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p>¿Está seguro que desea eliminar esta categoría?</p>
                    <p id="categoriaAEliminar"></p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger">Eliminar</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal Modificar -->
            <div class="modal fade" id="modificarModal" tabindex="-1" aria-labelledby="modificarModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modificarModalLabel">Modificar Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="nombreCategoria" class="form-label">Nombre de la categoría</label>
                      <input type="text" class="form-control" id="nombreCategoria">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Guardar cambios</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal Nueva Categoría -->
            <div class="modal fade" id="nuevaModal" tabindex="-1" aria-labelledby="nuevaModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="nuevaModalLabel">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="nuevaCategoria" class="form-label">Nombre de la nueva categoría</label>
                      <input type="text" class="form-control" id="nuevaCategoria">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Crear</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Tab de Tarifas -->
          <div id="tab2" class="tab-content d-none">
            <div class="ticket-summary">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Tarifas en bloques de tiempo</h3>
                <button class="btn-edit" data-bs-toggle="modal" data-bs-target="#agregarTarifaModal">
                  <i class="fas fa-plus"></i> Agregar Nueva tarifa
                </button>
              </div>
              <div class="table-responsive">
                <table class="table custom-table">
                  <thead>
                    <tr>
                      <th class="highlight-column"></th>
                      <th class="highlight-column">Día</th>
                      <th class="highlight-column">Horas</th>
                      <th class="highlight-column">Minuto</th>
                      <th class="highlight-column">Tolerancia(min)</th>
                      <th class="highlight-column">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="highlight-column">Hora</td>
                      <td></td>
                      <td>1</td>
                      <td></td>
                      <td>15</td>
                      <td>
                        <button class="btn-edit" data-bs-toggle="modal" data-bs-target="#editarTarifaModal">
                          <i class="fas fa-edit"></i> Editar
                        </button>
                      </td>
                    </tr>
                    <tr>
                      <td class="highlight-column">Dia</td>
                      <td>1</td>
                      <td></td>
                      <td></td>
                      <td>15</td>
                      <td>
                        <button class="btn-edit" data-bs-toggle="modal" data-bs-target="#editarTarifaModal">
                          <i class="fas fa-edit"></i> Editar
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Modal Editar Tarifa -->
              <div class="modal fade" id="editarTarifaModal" tabindex="-1" aria-labelledby="editarTarifaModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editarTarifaModalLabel">Tarifa x Hora</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <p class="text-muted small">Establece un Lapso de Tiempo. En la etiqueta describe brevemente el tiempo. Ej: 1 Hora, 15 Minutos, 7 Días, etc. Luego de establecer este Lapso de Tiempo no olvides definir los importes en PRECIOS.</p>

                      <div class="mb-3">
                        <label class="form-label">Etiqueta</label>
                        <input type="text" class="form-control" value="Hora">
                      </div>

                      <div class="row mb-3">
                        <div class="col">
                          <label class="form-label">Días</label>
                          <input type="number" class="form-control" value="0">
                        </div>
                        <div class="col">
                          <label class="form-label">Horas</label>
                          <input type="number" class="form-control" value="1">
                        </div>
                        <div class="col">
                          <label class="form-label">Minutos</label>
                          <input type="number" class="form-control" value="0">
                        </div>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Tolerancia en minutos</label>
                        <input type="text" class="form-control" value="15">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                      <button type="button" class="btn btn-primary">Guardar</button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Modal Agregar Tarifa -->
              <div class="modal fade" id="agregarTarifaModal" tabindex="-1" aria-labelledby="agregarTarifaModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="agregarTarifaModalLabel">Tarifa x Hora</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <p class="text-muted small">Establece un Lapso de Tiempo. En la etiqueta describe brevemente el tiempo. Ej: 1 Hora, 15 Minutos, 7 Días, etc. Luego de establecer este Lapso de Tiempo no olvides definir los importes en PRECIOS.</p>

                      <div class="mb-3">
                        <label class="form-label">Etiqueta</label>
                        <input type="text" class="form-control" value="">
                      </div>

                      <div class="row mb-3">
                        <div class="col">
                          <label class="form-label">Días</label>
                          <input type="number" class="form-control" value="0">
                        </div>
                        <div class="col">
                          <label class="form-label">Horas</label>
                          <input type="number" class="form-control" value="0">
                        </div>
                        <div class="col">
                          <label class="form-label">Minutos</label>
                          <input type="number" class="form-control" value="0">
                        </div>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Tolerancia en minutos</label>
                        <input type="text" class="form-control" value="0">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                      <button type="button" class="btn btn-primary">Guardar</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Tab de Precios -->
          <div id="tab3" class="tab-content d-none">
            <h3>Precios</h3>
            <p>Establece los precios para cada categoría de vehículo en cada tipo de tarifa</p>

            <div class="card p-4 mb-4">
              <div class="ticket-summary">
                <h5>Tarifas</h5>
                <div class="table-responsive">
                  <table class="table custom-table">
                    <thead>
                      <tr>
                        <th class="highlight-column"></th>
                        <th class="highlight-column">AUTO</th>
                        <th class="highlight-column">CAMIONETA</th>
                        <th class="highlight-column">MOTO</th>
                        <th class="highlight-column">MOTOCARRO</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="highlight-column">HORA</td>
                        <td><input type="number" class="form-control" value="1500"></td>
                        <td><input type="number" class="form-control" value="2000"></td>
                        <td><input type="number" class="form-control" value="1000"></td>
                        <td><input type="number" class="form-control" value="1500"></td>
                      </tr>
                      <tr>
                        <td class="highlight-column">DIA</td>
                        <td><input type="number" class="form-control" value="1500"></td>
                        <td><input type="number" class="form-control" value="2000"></td>
                        <td><input type="number" class="form-control" value="1000"></td>
                        <td><input type="number" class="form-control" value="1500"></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- Tab de Usuarios -->
          <div id="tab4" class="tab-content d-none">
            <h3>Usuarios</h3>
            <div class="card p-4 mb-4">
              <div class="ticket-summary">
                <div class="table-responsive">
                  <table class="table custom-table">
                    <thead>
                      <tr class="highlight-column">
                        <th>Usuario</th>
                        <th>Apellidos</th>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Estado Cuenta</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>joellizarazo</td>
                        <td>joel</td>
                        <td>lizarazo</td>
                        <td>ADMIN</td>
                        <td>12/03/24</td>
                        <td>
                          <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal">Editar</button>
                          <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#eliminarUsuarioModal">Eliminar</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="text-end mt-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">Crear Usuario</button>
              </div>
            </div>

            <!-- Modal Editar Usuario -->
            <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="editUsuario" class="form-label">Nombre de Usuario</label>
                      <input type="text" class="form-control" id="editUsuario" value="joellizarazo">
                    </div>
                    <div class="mb-3">
                      <label for="editApellidos" class="form-label">Apellidos</label>
                      <input type="text" class="form-control" id="editApellidos" value="joel">
                    </div>
                    <div class="mb-3">
                      <label for="editNombre" class="form-label">Nombre</label>
                      <input type="text" class="form-control" id="editNombre" value="lizarazo">
                    </div>
                    <div class="mb-3">
                      <label for="editRol" class="form-label">Rol</label>
                      <select class="form-select" id="editRol">
                        <option value="ADMIN" selected>Administrador</option>
                        <option value="USER">Usuario</option>
                      </select>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Guardar Cambios</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal Eliminar Usuario -->
            <div class="modal fade" id="eliminarUsuarioModal" tabindex="-1" aria-labelledby="eliminarUsuarioModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="eliminarUsuarioModalLabel">Eliminar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p>¿Está seguro que desea eliminar al usuario <strong>joellizarazo</strong>?</p>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger">Eliminar</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal Nuevo Usuario -->
            <div class="modal fade" id="nuevoUsuarioModal" tabindex="-1" aria-labelledby="nuevoUsuarioModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="nuevoUsuarioModalLabel">Crear Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="nombreUsuario" class="form-label">Nombre de Usuario</label>
                      <input type="text" class="form-control" id="nombreUsuario">
                    </div>
                    <div class="mb-3">
                      <label for="apellidos" class="form-label">Apellidos</label>
                      <input type="text" class="form-control" id="apellidos">
                    </div>
                    <div class="mb-3">
                      <label for="nombre" class="form-label">Nombre</label>
                      <input type="text" class="form-control" id="nombre">
                    </div>
                    <div class="mb-3">
                      <label for="password" class="form-label">Contraseña</label>
                      <input type="password" class="form-control" id="password">
                    </div>
                    <div class="mb-3">
                      <label for="rol" class="form-label">Rol</label>
                      <select class="form-select" id="rol">
                        <option value="ADMIN">Administrador</option>
                        <option value="USER">Usuario</option>
                      </select>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Guardar</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
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