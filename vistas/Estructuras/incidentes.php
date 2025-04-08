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
  <title>Incidentes | SmartPark</title>
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
  
  <!-- jQuery y SweetAlert2 -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
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
    
    /* Estilos para la sección de incidentes */
    .dropzone {
      border: 2px dashed #ccc;
      border-radius: 5px;
      padding: 20px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .dropzone:hover {
      border-color: #007bff;
      background-color: #f8f9fa;
    }
    
    .preview-container {
      display: flex;
      flex-wrap: wrap;
      margin-top: 15px;
    }
    
    .preview-item {
      position: relative;
      margin: 5px;
      width: 100px;
      height: 100px;
      overflow: hidden;
      border-radius: 4px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    }
    
    .preview-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .preview-item .remove-btn {
      position: absolute;
      top: 5px;
      right: 5px;
      background: rgba(255,255,255,0.7);
      border-radius: 50%;
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 12px;
      color: #dc3545;
    }
    
    .file-preview {
      text-align: center;
      padding: 20px;
    }
    
    .file-info {
      padding: 10px;
      font-size: 12px;
      color: #666;
    }
    
    .file-name {
      font-weight: bold;
    }
    
    .file-size {
      color: #999;
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

      <div class="row">
        <!-- Formulario de Registro de Incidentes -->
        <div class="col-md-6">
          <div class="card shadow-sm">
            <div class="card-header text-white py-3">
              <div class="d-flex align-items-center">
                <span class="rounded-circle bg-light p-2 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                  <i class="fas fa-exclamation-triangle text-dark"></i>
                </span>
                <h4 class="mb-0">Nuevo Incidente</h4>
              </div>
            </div>
            <div class="card-body">
              <form id="formIncidente" enctype="multipart/form-data">
                <div class="mb-3">
                  <label for="id_registro" class="form-label">Ticket Asociado</label>
                  <select class="form-select" id="id_registro" name="id_registro">
                    <option value="">Seleccione un ticket</option>
                    <!-- Se cargará dinámicamente -->
                  </select>
                </div>
                <div class="mb-3">
                  <label for="id_cliente" class="form-label">Cliente</label>
                  <select class="form-select" id="id_cliente" name="id_cliente">
                    <option value="">Seleccione un cliente</option>
                    <!-- Se cargará dinámicamente -->
                  </select>
                </div>
                <div class="mb-3">
                  <label for="tipo" class="form-label">Tipo de Incidente</label>
                  <select class="form-select" id="tipo" name="tipo" required>
                    <option value="">Seleccione un tipo</option>
                    <option value="robo">Robo</option>
                    <option value="daño">Daño a vehículo</option>
                    <option value="mal uso de espacios">Mal uso de espacios</option>
                    <option value="perdida">Pérdida</option>
                    <option value="PQR">Peticiones, Quejas y Reclamos</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="descripcion" class="form-label">Descripción</label>
                  <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                  <label for="evidencia" class="form-label">Evidencia (opcional)</label>
                  <div id="evidenciaDropzone" class="dropzone">
                    <div class="dz-message">
                      <i class="fas fa-cloud-upload-alt fa-3x mb-2"></i>
                      <p>Arrastra y suelta archivos aquí o haz clic para seleccionar</p>
                      <p class="small text-muted">Imágenes, videos o documentos (máx. 10MB)</p>
                    </div>
                    <input type="file" id="evidencia" name="evidencia[]" multiple style="display: none;">
                  </div>
                  <div id="previewContainer" class="preview-container">
                    <!-- Previsualizaciones se mostrarán aquí -->
                  </div>
                </div>
                <div class="d-grid">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Registrar Incidente
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        
        <!-- Lista de Incidentes -->
        <div class="col-md-6">
          <div class="card shadow-sm">
            <div class="card-header text-white py-3">
              <div class="d-flex align-items-center">
                <span class="rounded-circle bg-light p-2 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                  <i class="fas fa-list text-dark"></i>
                </span>
                <h4 class="mb-0">Incidentes</h4>
              </div>
            </div>
            <div class="card-body p-0">
              <!-- Tabs mejorados con mejor espaciado y centrado -->
              <div class="pt-4 pb-2">
                <ul class="nav nav-tabs nav-fill justify-content-center" id="incidentesTabs" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active px-4 py-3" id="incidentes-pendientes-tab" data-bs-toggle="tab" data-bs-target="#incidentes-pendientes" type="button" role="tab" aria-controls="incidentes-pendientes" aria-selected="true">
                      <i class="fas fa-clock me-2"></i>Pendientes
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3" id="incidentes-resueltos-tab" data-bs-toggle="tab" data-bs-target="#incidentes-resueltos" type="button" role="tab" aria-controls="incidentes-resueltos" aria-selected="false">
                      <i class="fas fa-check-circle me-2"></i>Resueltos
                    </button>
                  </li>
                </ul>
              </div>
              <div class="tab-content" id="incidentesTabsContent">
                <div class="tab-pane fade show active" id="incidentes-pendientes" role="tabpanel" aria-labelledby="incidentes-pendientes-tab">
                  <div class="table-responsive">
                    <table class="table table-hover mb-0">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Tipo</th>
                          <th>Fecha</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody id="listaIncidentesPendientes">
                        <!-- Se cargará dinámicamente -->
                      </tbody>
                    </table>
                  </div>
                  <!-- Paginación para incidentes pendientes -->
                  <div class="d-flex justify-content-between align-items-center p-3 border-top">
                    <div>
                      <span id="totalIncidentesPendientes" class="text-muted">0 incidentes</span>
                    </div>
                    <div>
                      <nav aria-label="Paginación de incidentes pendientes">
                        <ul class="pagination pagination-sm mb-0" id="paginacionPendientes">
                          <!-- Se generará dinámicamente -->
                        </ul>
                      </nav>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="incidentes-resueltos" role="tabpanel" aria-labelledby="incidentes-resueltos-tab">
                  <div class="table-responsive">
                    <table class="table table-hover mb-0">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Tipo</th>
                          <th>Fecha</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody id="listaIncidentesResueltos">
                        <!-- Se cargará dinámicamente -->
                      </tbody>
                    </table>
                  </div>
                  <!-- Paginación para incidentes resueltos -->
                  <div class="d-flex justify-content-between align-items-center p-3 border-top">
                    <div>
                      <span id="totalIncidentesResueltos" class="text-muted">0 incidentes</span>
                    </div>
                    <div>
                      <nav aria-label="Paginación de incidentes resueltos">
                        <ul class="pagination pagination-sm mb-0" id="paginacionResueltos">
                          <!-- Se generará dinámicamente -->
                        </ul>
                      </nav>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Modal para ver detalles del incidente -->
      <div class="modal fade" id="detalleIncidenteModal" tabindex="-1" aria-labelledby="detalleIncidenteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="detalleIncidenteModalLabel">Detalles del Incidente</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detalleIncidenteBody">
              <!-- Se cargará dinámicamente -->
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="button" class="btn btn-success" id="btnMarcarResuelto">
                <i class="fas fa-check-circle me-2"></i>Marcar como Resuelto
              </button>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
  <!-- [ Main Content ] end -->

  <!-- Required Js -->
  <script src="../assets/js/plugins/popper.min.js"></script>
  <script src="../assets/js/plugins/simplebar.min.js"></script>
  <script src="../assets/js/plugins/bootstrap.min.js"></script>
  <script src="../assets/js/fonts/custom-font.js"></script>
  <script src="../assets/js/pcoded.js"></script>
  <script src="../assets/js/plugins/feather.min.js"></script>
  
  <script>
    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
      // Cargar tickets activos
      cargarTicketsActivos();
      
      // Cargar clientes
      cargarClientes();
      
      // Cargar incidentes pendientes
      cargarIncidentesPendientes();
      
      // Cargar incidentes resueltos
      cargarIncidentesResueltos();
      
      // Configurar dropzone para evidencias
      const dropzone = document.getElementById('evidenciaDropzone');
      const fileInput = document.getElementById('evidencia');
      
      dropzone.addEventListener('click', function() {
        fileInput.click();
      });
      
      dropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropzone.classList.add('border-primary');
      });
      
      dropzone.addEventListener('dragleave', function() {
        dropzone.classList.remove('border-primary');
      });
      
      dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropzone.classList.remove('border-primary');
        
        if (e.dataTransfer.files.length) {
          fileInput.files = e.dataTransfer.files;
          mostrarPrevisualizaciones(fileInput.files);
        }
      });
      
      fileInput.addEventListener('change', function() {
        mostrarPrevisualizaciones(this.files);
      });
      
      // Configurar envío del formulario
      document.getElementById('formIncidente').addEventListener('submit', function(e) {
        e.preventDefault();
        registrarIncidente();
      });
    });
    
    // Función para mostrar previsualizaciones de archivos
    function mostrarPrevisualizaciones(files) {
      const previewContainer = document.getElementById('previewContainer');
      previewContainer.innerHTML = '';
      
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        if (!file.type.match('image.*') && !file.type.match('video.*')) {
          // Para otros tipos de archivos, mostrar un icono genérico
          const previewItem = document.createElement('div');
          previewItem.className = 'preview-item';
          previewItem.innerHTML = `
            <div class="file-preview">
              <i class="fas fa-file fa-2x text-primary mb-2"></i>
              <p class="file-name">${file.name}</p>
            </div>
            <div class="remove-btn" data-index="${i}"><i class="fas fa-times"></i></div>
          `;
          previewContainer.appendChild(previewItem);
          continue;
        }
        
        const reader = new FileReader();
        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';
        
        reader.onload = function(e) {
          if (file.type.match('image.*')) {
            previewItem.innerHTML = `
              <img src="${e.target.result}" alt="Preview">
              <div class="file-info">
                <span class="file-name">${file.name}</span>
                <span class="file-size">${formatFileSize(file.size)}</span>
              </div>
              <div class="remove-btn" data-index="${i}"><i class="fas fa-times"></i></div>
            `;
          } else if (file.type.match('video.*')) {
            previewItem.innerHTML = `
              <video src="${e.target.result}" style="width:100%;height:100%;" controls></video>
              <div class="file-info">
                <span class="file-name">${file.name}</span>
                <span class="file-size">${formatFileSize(file.size)}</span>
              </div>
              <div class="remove-btn" data-index="${i}"><i class="fas fa-times"></i></div>
            `;
          }
        };
        
        reader.readAsDataURL(file);
        previewContainer.appendChild(previewItem);
      }
      
      // Agregar eventos para eliminar archivos
      setTimeout(function() {
        document.querySelectorAll('.remove-btn').forEach(btn => {
          btn.addEventListener('click', function() {
            this.parentElement.remove();
            // No se puede eliminar directamente de FileList, se maneja en el backend
          });
        });
      }, 100);
    }
    
    // Función para formatear el tamaño de archivo
    function formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Función para cargar tickets activos
    function cargarTicketsActivos() {
      fetch('../../controladores/incidentes.php?accion=obtener_tickets_activos')
        .then(response => response.json())
        .then(data => {
          const selectTicket = document.getElementById('id_registro');
          selectTicket.innerHTML = '<option value="">Seleccione un ticket</option>';
          
          data.forEach(ticket => {
            const option = document.createElement('option');
            option.value = ticket.id_registro;
            option.textContent = `#${ticket.id_registro} - ${ticket.placa} (${ticket.tipo_vehiculo})`;
            
            // Guardar información del cliente como atributos de datos
            if (ticket.id_cliente) {
              option.setAttribute('data-cliente-id', ticket.id_cliente);
              if (ticket.cliente_nombre) {
                option.setAttribute('data-cliente-nombre', ticket.cliente_nombre);
                option.setAttribute('data-cliente-telefono', ticket.cliente_telefono || '');
              }
            }
            
            selectTicket.appendChild(option);
          });
          
          // Agregar evento de cambio para asociar automáticamente el cliente
          selectTicket.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const clienteSelect = document.getElementById('id_cliente');
            
            // Habilitar el selector de cliente por defecto
            clienteSelect.disabled = false;
            
            // Si no se ha seleccionado ningún ticket, dejar el selector de cliente habilitado
            if (this.value === '') {
              clienteSelect.value = '';
              return;
            }
            
            if (selectedOption.hasAttribute('data-cliente-id')) {
              // Si el ticket tiene cliente asociado, seleccionarlo automáticamente
              const clienteId = selectedOption.getAttribute('data-cliente-id');
              clienteSelect.value = clienteId;
              
              // Deshabilitar el selector de cliente ya que ya tenemos el cliente correcto
              clienteSelect.disabled = true;
              
              // Mostrar información del cliente
              const clienteNombre = selectedOption.getAttribute('data-cliente-nombre');
              if (clienteNombre) {
                mostrarNotificacion('Cliente asociado', `Se ha asociado automáticamente el cliente: ${clienteNombre}`, 'info');
              }
            } else {
              // Si no tiene cliente, deshabilitar el selector de cliente
              clienteSelect.value = '';
              clienteSelect.disabled = true;
              mostrarNotificacion('Sin cliente', 'El vehículo seleccionado no tiene un cliente asociado', 'warning');
            }
          });
        })
        .catch(error => {
          console.error('Error al cargar tickets:', error);
          mostrarNotificacion('Error', 'No se pudieron cargar los tickets activos', 'error');
        });
    }
    
    // Función para cargar clientes
    function cargarClientes() {
      fetch('../../controladores/incidentes.php?accion=obtener_clientes')
        .then(response => response.json())
        .then(data => {
          const selectCliente = document.getElementById('id_cliente');
          selectCliente.innerHTML = '<option value="">Seleccione un cliente</option>';
          
          data.forEach(cliente => {
            const option = document.createElement('option');
            option.value = cliente.id_cliente;
            option.textContent = `${cliente.nombre} (${cliente.telefono || 'Sin teléfono'})`;
            selectCliente.appendChild(option);
          });
          
          // Inicialmente habilitar el selector de cliente
          selectCliente.disabled = false;
        })
        .catch(error => {
          console.error('Error al cargar clientes:', error);
          mostrarNotificacion('Error', 'No se pudieron cargar los clientes', 'error');
        });
    }
    
    // Función para cargar incidentes pendientes
    function cargarIncidentesPendientes(pagina = 1) {
      fetch(`../../controladores/incidentes.php?accion=obtener_incidentes_pendientes&pagina=${pagina}`)
        .then(response => response.json())
        .then(data => {
          const listaIncidentesPendientes = document.getElementById('listaIncidentesPendientes');
          
          if (!data.incidentes || !Array.isArray(data.incidentes) || data.incidentes.length === 0) {
            listaIncidentesPendientes.innerHTML = '<tr><td colspan="4" class="text-center">No hay incidentes pendientes</td></tr>';
            document.getElementById('totalIncidentesPendientes').textContent = '0 incidentes';
            document.getElementById('paginacionPendientes').innerHTML = '';
            return;
          }
          
          listaIncidentesPendientes.innerHTML = '';
          
          data.incidentes.forEach(incidente => {
            const fecha = new Date(incidente.fecha_registro);
            const fechaFormateada = fecha.toLocaleDateString() + ' ' + fecha.toLocaleTimeString();
            
            listaIncidentesPendientes.innerHTML += `
              <tr>
                <td>${incidente.id_incidente}</td>
                <td>
                  <span class="badge ${getBadgeClass(incidente.tipo)}">${incidente.tipo}</span>
                </td>
                <td>${fechaFormateada}</td>
                <td>
                  <button class="btn btn-sm btn-info me-2" onclick="verDetalleIncidente(${incidente.id_incidente})">
                    <i class="fas fa-eye"></i>
                  </button>
                </td>
              </tr>
            `;
          });
          
          // Actualizar el total de incidentes pendientes
          document.getElementById('totalIncidentesPendientes').textContent = `${data.paginacion.total} incidentes`;
          
          // Generar la paginación
          generarPaginacion(
            'paginacionPendientes', 
            data.paginacion.paginaActual, 
            data.paginacion.totalPaginas, 
            (pagina) => cargarIncidentesPendientes(pagina)
          );
        })
        .catch(error => {
          console.error('Error al cargar incidentes pendientes:', error);
          mostrarNotificacion('Error', 'No se pudieron cargar los incidentes pendientes', 'error');
        });
    }
    
    // Función para cargar incidentes resueltos
    function cargarIncidentesResueltos(pagina = 1) {
      fetch(`../../controladores/incidentes.php?accion=obtener_incidentes_resueltos&pagina=${pagina}`)
        .then(response => response.json())
        .then(data => {
          const listaIncidentesResueltos = document.getElementById('listaIncidentesResueltos');
          
          if (!data.incidentes || !Array.isArray(data.incidentes) || data.incidentes.length === 0) {
            listaIncidentesResueltos.innerHTML = '<tr><td colspan="4" class="text-center">No hay incidentes resueltos</td></tr>';
            document.getElementById('totalIncidentesResueltos').textContent = '0 incidentes';
            document.getElementById('paginacionResueltos').innerHTML = '';
            return;
          }
          
          listaIncidentesResueltos.innerHTML = '';
          
          data.incidentes.forEach(incidente => {
            const fecha = new Date(incidente.fecha_registro);
            const fechaFormateada = fecha.toLocaleDateString() + ' ' + fecha.toLocaleTimeString();
            
            listaIncidentesResueltos.innerHTML += `
              <tr>
                <td>${incidente.id_incidente}</td>
                <td>
                  <span class="badge ${getBadgeClass(incidente.tipo)}">${incidente.tipo}</span>
                </td>
                <td>${fechaFormateada}</td>
                <td>
                  <button class="btn btn-sm btn-info" onclick="verDetalleIncidente(${incidente.id_incidente})">
                    <i class="fas fa-eye"></i>
                  </button>
                </td>
              </tr>
            `;
          });
          
          // Actualizar el total de incidentes resueltos
          document.getElementById('totalIncidentesResueltos').textContent = `${data.paginacion.total} incidentes`;
          
          // Generar la paginación
          generarPaginacion(
            'paginacionResueltos', 
            data.paginacion.paginaActual, 
            data.paginacion.totalPaginas, 
            (pagina) => cargarIncidentesResueltos(pagina)
          );
        })
        .catch(error => {
          console.error('Error al cargar incidentes resueltos:', error);
          mostrarNotificacion('Error', 'No se pudieron cargar los incidentes resueltos', 'error');
        });
    }
    
    // Función para obtener la clase del badge según el tipo de incidente
    function getBadgeClass(tipo) {
      switch(tipo) {
        case 'robo':
          return 'bg-danger';
        case 'daño':
          return 'bg-warning';
        case 'mal uso de espacios':
          return 'bg-info';
        case 'perdida':
          return 'bg-warning text-dark';
        case 'PQR':
          return 'bg-primary';
        default:
          return 'bg-secondary';
      }
    }
    
    // Función para registrar un nuevo incidente
    function registrarIncidente() {
      const formData = new FormData(document.getElementById('formIncidente'));
      formData.append('accion', 'registrar_incidente');
      
      fetch('../../controladores/incidentes.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          mostrarNotificacion('Éxito', 'Incidente registrado correctamente', 'success');
          document.getElementById('formIncidente').reset();
          document.getElementById('previewContainer').innerHTML = '';
          cargarIncidentesPendientes();
        } else {
          mostrarNotificacion('Error', data.message || 'No se pudo registrar el incidente', 'error');
        }
      })
      .catch(error => {
        console.error('Error al registrar incidente:', error);
        mostrarNotificacion('Error', 'Ocurrió un error al procesar la solicitud', 'error');
      });
    }
    
    // Función para ver detalle de un incidente
    function verDetalleIncidente(idIncidente) {
      // Mostrar un indicador de carga
      Swal.fire({
        title: 'Cargando...',
        text: 'Obteniendo detalles del incidente',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      
      fetch(`../../controladores/incidentes.php?accion=obtener_detalle_incidente&id=${idIncidente}`)
        .then(response => {
          if (!response.ok) {
            throw new Error('Error en la respuesta del servidor: ' + response.status);
          }
          return response.json();
        })
        .then(data => {
          // Cerrar el indicador de carga
          Swal.close();
          
          console.log('Datos del incidente:', data); // Para depuración
          
          // Verificar si hay un mensaje de error
          if (data.success === false) {
            mostrarNotificacion('Error', data.message || 'No se pudo obtener el detalle del incidente', 'error');
            return;
          }
          
          if (data) {
            const fecha = new Date(data.fecha_registro);
            const fechaFormateada = fecha.toLocaleDateString() + ' ' + fecha.toLocaleTimeString();
            
            let evidenciasHTML = '';
            
            // Verificar si hay evidencias
            if (data.evidencias && Array.isArray(data.evidencias) && data.evidencias.length > 0) {
              evidenciasHTML = '<div class="mt-3"><h6>Evidencias:</h6><div class="row">';
              
              data.evidencias.forEach(evidencia => {
                // Usar la URL completa para mostrar la evidencia
                const urlEvidencia = evidencia.url_completa || evidencia.url || '';
                const tipoEvidencia = evidencia.tipo || '';
                const nombreEvidencia = evidencia.nombre || 'Archivo';
                
                if (tipoEvidencia.startsWith('image/')) {
                  evidenciasHTML += `
                    <div class="col-md-4 mb-2">
                      <div class="card">
                        <a href="${urlEvidencia}" target="_blank">
                          <img src="${urlEvidencia}" class="img-fluid rounded" alt="Evidencia">
                        </a>
                        <div class="card-footer p-2 bg-light">
                          <small class="text-muted">${nombreEvidencia}</small>
                        </div>
                      </div>
                    </div>
                  `;
                } else if (tipoEvidencia.startsWith('video/')) {
                  evidenciasHTML += `
                    <div class="col-md-6 mb-2">
                      <div class="card">
                        <video src="${urlEvidencia}" controls class="img-fluid rounded"></video>
                        <div class="card-footer p-2 bg-light">
                          <small class="text-muted">${nombreEvidencia}</small>
                        </div>
                      </div>
                    </div>
                  `;
                } else {
                  evidenciasHTML += `
                    <div class="col-md-4 mb-2">
                      <div class="card">
                        <div class="card-body text-center">
                          <i class="fas fa-file fa-3x text-primary mb-2"></i>
                          <p class="mb-0"><a href="${urlEvidencia}" target="_blank">${nombreEvidencia}</a></p>
                        </div>
                      </div>
                    </div>
                  `;
                }
              });
              
              evidenciasHTML += '</div></div>';
            } else if (data.evidencia) {
              // Si no hay array de evidencias pero hay un campo evidencia, intentar procesarlo
              try {
                let evidenciasDirectas;
                
                // Verificar si ya es un objeto o si es un string JSON
                if (typeof data.evidencia === 'string') {
                  evidenciasDirectas = JSON.parse(data.evidencia);
                } else {
                  evidenciasDirectas = data.evidencia;
                }
                
                if (Array.isArray(evidenciasDirectas) && evidenciasDirectas.length > 0) {
                  evidenciasHTML = '<div class="mt-3"><h6>Evidencias:</h6><div class="row">';
                  
                  evidenciasDirectas.forEach(evidencia => {
                    const nombreEvidencia = evidencia.nombre || 'Archivo';
                    const tipoEvidencia = evidencia.tipo || '';
                    
                    // Construir URL completa
                    let urlEvidencia = '';
                    if (evidencia.url) {
                      urlEvidencia = '../../uploads/evidencias/incidentes/' + evidencia.url;
                    }
                    
                    evidenciasHTML += `
                      <div class="col-md-4 mb-2">
                        <div class="card">
                          <div class="card-body text-center">
                            <i class="fas fa-file fa-3x text-primary mb-2"></i>
                            <p class="mb-0">${nombreEvidencia}</p>
                            <small class="text-muted">${tipoEvidencia}</small>
                          </div>
                        </div>
                      </div>
                    `;
                  });
                  
                  evidenciasHTML += '</div></div>';
                } else {
                  evidenciasHTML = '<p class="text-muted mt-3">No se pudieron procesar las evidencias</p>';
                  console.error('Formato de evidencias no reconocido:', data.evidencia);
                }
              } catch (error) {
                console.error('Error al procesar evidencias:', error);
                evidenciasHTML = '<p class="text-muted mt-3">Error al procesar las evidencias</p>';
              }
            } else {
              evidenciasHTML = '<p class="text-muted mt-3">No hay evidencias adjuntas</p>';
            }
            
            let clienteInfo = 'No asociado';
            if (data.cliente) {
              clienteInfo = `${data.cliente.nombre} (${data.cliente.telefono || 'Sin teléfono'})`;
            }
            
            let ticketInfo = 'No asociado';
            if (data.ticket) {
              ticketInfo = `#${data.ticket.id_registro} - ${data.ticket.placa} (${data.ticket.tipo_vehiculo})`;
            }
            
            // Mostrar u ocultar el botón de marcar como resuelto según el estado
            const btnMarcarResuelto = document.getElementById('btnMarcarResuelto');
            if (data.estado === 'resuelto') {
              btnMarcarResuelto.style.display = 'none';
            } else {
              btnMarcarResuelto.style.display = 'block';
              
              // Configurar el evento para marcar como resuelto
              btnMarcarResuelto.onclick = function() {
                marcarIncidenteResuelto(data.id_incidente);
              };
            }
            
            // Mostrar el estado del incidente
            let estadoHTML = '';
            if (data.estado === 'resuelto') {
              estadoHTML = `<span class="badge bg-success">Resuelto</span>`;
            } else {
              estadoHTML = `<span class="badge bg-warning">Pendiente</span>`;
            }
            
            document.getElementById('detalleIncidenteBody').innerHTML = `
              <div class="row">
                <div class="col-md-6">
                  <p><strong>ID:</strong> ${data.id_incidente}</p>
                  <p><strong>Tipo:</strong> <span class="badge ${getBadgeClass(data.tipo)}">${data.tipo}</span></p>
                  <p><strong>Estado:</strong> ${estadoHTML}</p>
                  <p><strong>Fecha:</strong> ${fechaFormateada}</p>
                </div>
                <div class="col-md-6">
                  <p><strong>Cliente:</strong> ${clienteInfo}</p>
                  <p><strong>Ticket:</strong> ${ticketInfo}</p>
                </div>
              </div>
              <div class="mt-3">
                <h6>Descripción:</h6>
                <p>${data.descripcion}</p>
              </div>
              ${evidenciasHTML}
            `;
            
            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('detalleIncidenteModal'));
            modal.show();
          } else {
            mostrarNotificacion('Error', 'No se pudo obtener el detalle del incidente', 'error');
          }
        })
        .catch(error => {
          // Cerrar el indicador de carga
          Swal.close();
          
          console.error('Error al obtener detalle del incidente:', error);
          mostrarNotificacion('Error', 'Ocurrió un error al procesar la solicitud', 'error');
        });
    }
    
    // Función para marcar un incidente como resuelto
    function marcarIncidenteResuelto(idIncidente) {
      // Cerrar el modal de visualización primero
      const modalDetalle = bootstrap.Modal.getInstance(document.getElementById('detalleIncidenteModal'));
      if (modalDetalle) {
        modalDetalle.hide();
      }
      
      // Pequeña pausa para asegurar que el modal se cierre completamente
      setTimeout(() => {
        // Mostrar confirmación antes de marcar como resuelto
        Swal.fire({
          title: '¿Marcar como resuelto?',
          text: 'Este incidente se moverá al historial de incidentes resueltos',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#28a745',
          cancelButtonColor: '#6c757d',
          confirmButtonText: '<i class="fas fa-check-circle me-2"></i>Sí, marcar como resuelto',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            // Crear FormData para enviar la solicitud
            const formData = new FormData();
            formData.append('accion', 'marcar_incidente_resuelto');
            formData.append('id', idIncidente);
            
            // Enviar solicitud al servidor
            fetch('../../controladores/incidentes.php', {
              method: 'POST',
              body: formData
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                // Mostrar notificación de éxito con el estilo preferido por el usuario
                mostrarNotificacion('Éxito', 'Incidente marcado como resuelto correctamente', 'success');
                
                // Recargar las listas de incidentes
                cargarIncidentesPendientes();
                cargarIncidentesResueltos();
              } else {
                mostrarNotificacion('Error', data.message || 'No se pudo marcar el incidente como resuelto', 'error');
              }
            })
            .catch(error => {
              console.error('Error al marcar incidente como resuelto:', error);
              mostrarNotificacion('Error', 'Ocurrió un error al procesar la solicitud', 'error');
            });
          } else {
            // Si el usuario cancela, volver a mostrar el modal de detalle
            const nuevoModalDetalle = new bootstrap.Modal(document.getElementById('detalleIncidenteModal'));
            nuevoModalDetalle.show();
          }
        });
      }, 300); // 300ms de espera para asegurar que el modal anterior se cierre
    }
    
    // Función para generar los controles de paginación
    function generarPaginacion(contenedorId, paginaActual, totalPaginas, callback) {
      const paginacionContainer = document.getElementById(contenedorId);
      paginacionContainer.innerHTML = '';
      
      if (totalPaginas <= 1) {
        return;
      }
      
      // Botón "Anterior"
      const btnAnterior = document.createElement('li');
      btnAnterior.className = `page-item ${paginaActual === 1 ? 'disabled' : ''}`;
      btnAnterior.innerHTML = `<a class="page-link" href="#" aria-label="Anterior"><span aria-hidden="true">&laquo;</span></a>`;
      
      if (paginaActual > 1) {
        btnAnterior.addEventListener('click', (e) => {
          e.preventDefault();
          callback(paginaActual - 1);
        });
      }
      
      paginacionContainer.appendChild(btnAnterior);
      
      // Determinar qué páginas mostrar
      let startPage = Math.max(1, paginaActual - 2);
      let endPage = Math.min(totalPaginas, startPage + 4);
      
      if (endPage - startPage < 4) {
        startPage = Math.max(1, endPage - 4);
      }
      
      // Botones de páginas
      for (let i = startPage; i <= endPage; i++) {
        const btnPagina = document.createElement('li');
        btnPagina.className = `page-item ${i === paginaActual ? 'active' : ''}`;
        btnPagina.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        
        btnPagina.addEventListener('click', (e) => {
          e.preventDefault();
          if (i !== paginaActual) {
            callback(i);
          }
        });
        
        paginacionContainer.appendChild(btnPagina);
      }
      
      // Botón "Siguiente"
      const btnSiguiente = document.createElement('li');
      btnSiguiente.className = `page-item ${paginaActual === totalPaginas ? 'disabled' : ''}`;
      btnSiguiente.innerHTML = `<a class="page-link" href="#" aria-label="Siguiente"><span aria-hidden="true">&raquo;</span></a>`;
      
      if (paginaActual < totalPaginas) {
        btnSiguiente.addEventListener('click', (e) => {
          e.preventDefault();
          callback(paginaActual + 1);
        });
      }
      
      paginacionContainer.appendChild(btnSiguiente);
    }
    
    // Función para mostrar notificaciones
    function mostrarNotificacion(titulo, mensaje, tipo) {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: tipo,
        title: titulo,
        text: mensaje,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: {
          popup: `colored-toast swal2-icon-${tipo}`,
          title: 'toast-title',
          content: 'toast-content'
        }
      });
    }
  </script>
</body>
<!-- [Body] end -->

</html>
