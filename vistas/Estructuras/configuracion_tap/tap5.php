<!-- Tab de Medios de Pago -->
<div id="tab5" class="tab-content d-none">
  <h3>Medios de Pago</h3>
  <p class="text-muted small">Gestione los métodos de pago disponibles en el sistema</p>
  
  <div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h5 class="mb-0">Métodos de Pago Disponibles</h5>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoMetodo">
        <i class="fas fa-plus me-1"></i> Nuevo
      </button>
    </div>
    
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Estado</th>
            <th>Fecha Creación</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Consulta para obtener todos los métodos de pago
          $sql = "SELECT * FROM metodos_pago ORDER BY nombre";
          $resultado = $conexion->query($sql);
          
          if ($resultado && $resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
              $estado = $row['activo'] ? 
                '<span class="badge bg-success">Activo</span>' : 
                '<span class="badge bg-danger">Inactivo</span>';
              
              echo '<tr>
                      <td>' . $row['id_metodo'] . '</td>
                      <td>' . htmlspecialchars($row['nombre']) . '</td>
                      <td>' . $estado . '</td>
                      <td>' . date('d/m/Y', strtotime($row['fecha_creacion'])) . '</td>
                      <td>
                        <div class="btn-group">
                          <button type="button" class="btn btn-sm btn-info editar-metodo me-2" 
                            data-id="' . $row['id_metodo'] . '" 
                            data-nombre="' . htmlspecialchars($row['nombre']) . '"
                            data-activo="' . $row['activo'] . '"
                            data-bs-toggle="modal" data-bs-target="#modalEditarMetodo">
                            <i class="fas fa-edit"></i>
                          </button>
                          <button type="button" class="btn btn-sm ' . ($row['activo'] ? 'btn-danger' : 'btn-success') . ' cambiar-estado me-2" 
                            data-id="' . $row['id_metodo'] . '" 
                            data-activo="' . $row['activo'] . '">
                            <i class="fas ' . ($row['activo'] ? 'fa-ban' : 'fa-check') . '"></i>
                          </button>
                        </div>
                      </td>
                    </tr>';
            }
          } else {
            echo '<tr><td colspan="5" class="text-center">No hay métodos de pago registrados</td></tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal para Nuevo Método de Pago -->
<div class="modal fade" id="modalNuevoMetodo" tabindex="-1" aria-labelledby="modalNuevoMetodoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNuevoMetodoLabel">Nuevo Método de Pago</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formNuevoMetodo">
          <div class="mb-3">
            <label for="nombreNuevoMetodo" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombreNuevoMetodo" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarNuevoMetodo">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Editar Método de Pago -->
<div class="modal fade" id="modalEditarMetodo" tabindex="-1" aria-labelledby="modalEditarMetodoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarMetodoLabel">Editar Método de Pago</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formEditarMetodo">
          <input type="hidden" id="idMetodo">
          <div class="mb-3">
            <label for="nombreMetodo" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombreMetodo" required>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="activoMetodo">
              <label class="form-check-label" for="activoMetodo">
                Activo
              </label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnActualizarMetodo">Actualizar</button>
      </div>
    </div>
  </div>
</div>

<!-- Script para gestionar los métodos de pago -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Guardar nuevo método de pago
    document.getElementById('btnGuardarNuevoMetodo').addEventListener('click', function() {
      const nombre = document.getElementById('nombreNuevoMetodo').value.trim();
      
      if (!nombre) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'El nombre del método de pago es obligatorio',
          customClass: {
            popup: 'colored-toast'
          }
        });
        return;
      }
      
      // Enviar solicitud AJAX para guardar
      fetch('../../controladores/guardar_metodo_pago.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'nombre=' + encodeURIComponent(nombre)
      })
      .then(response => response.json())
      .then(data => {
        if (data.exito) {
          Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: data.mensaje,
            customClass: {
              popup: 'colored-toast'
            }
          }).then(() => {
            // Recargar la página
            window.location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.mensaje,
            customClass: {
              popup: 'colored-toast'
            }
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Ha ocurrido un error al procesar la solicitud',
          customClass: {
            popup: 'colored-toast'
          }
        });
      });
    });
    
    // Cargar datos para editar
    document.querySelectorAll('.editar-metodo').forEach(boton => {
      boton.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const nombre = this.getAttribute('data-nombre');
        const activo = this.getAttribute('data-activo') === '1';
        
        document.getElementById('idMetodo').value = id;
        document.getElementById('nombreMetodo').value = nombre;
        document.getElementById('activoMetodo').checked = activo;
      });
    });
    
    // Actualizar método de pago
    document.getElementById('btnActualizarMetodo').addEventListener('click', function() {
      const id = document.getElementById('idMetodo').value;
      const nombre = document.getElementById('nombreMetodo').value.trim();
      const activo = document.getElementById('activoMetodo').checked ? 1 : 0;
      
      if (!nombre) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'El nombre del método de pago es obligatorio',
          customClass: {
            popup: 'colored-toast'
          }
        });
        return;
      }
      
      // Enviar solicitud AJAX para actualizar
      fetch('../../controladores/actualizar_metodo_pago.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + encodeURIComponent(id) + '&nombre=' + encodeURIComponent(nombre) + '&activo=' + encodeURIComponent(activo)
      })
      .then(response => response.json())
      .then(data => {
        if (data.exito) {
          Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: data.mensaje,
            customClass: {
              popup: 'colored-toast'
            }
          }).then(() => {
            // Recargar la página
            window.location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.mensaje,
            customClass: {
              popup: 'colored-toast'
            }
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Ha ocurrido un error al procesar la solicitud',
          customClass: {
            popup: 'colored-toast'
          }
        });
      });
    });
    
    // Manejar cambio de estado (activar/desactivar)
    document.querySelectorAll('.cambiar-estado').forEach(boton => {
      boton.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const activo = this.getAttribute('data-activo') === '1' ? 0 : 1;
        const accion = activo ? 'activar' : 'desactivar';
        
        Swal.fire({
          title: '¿Está seguro?',
          text: `¿Desea ${accion} este método de pago?`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, confirmar',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            // Enviar solicitud AJAX para cambiar estado
            fetch('../../controladores/cambiar_estado_metodo_pago.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: 'id=' + encodeURIComponent(id) + '&activo=' + encodeURIComponent(activo)
            })
            .then(response => response.json())
            .then(data => {
              if (data.exito) {
                Swal.fire({
                  icon: 'success',
                  title: 'Éxito',
                  text: data.mensaje,
                  customClass: {
                    popup: 'colored-toast'
                  }
                }).then(() => {
                  // Recargar la página
                  window.location.reload();
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: data.mensaje,
                  customClass: {
                    popup: 'colored-toast'
                  }
                });
              }
            })
            .catch(error => {
              console.error('Error:', error);
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ha ocurrido un error al procesar la solicitud',
                customClass: {
                  popup: 'colored-toast'
                }
              });
            });
          }
        });
      });
    });
  });
</script>