//funcion para actualizar la hora y el importe del cliente constantemente en pantalla
function actualizarTiempoYCosto() {
  const elementosTiempo = document.querySelectorAll(".tiempo-transcurrido");
  const elementosCosto = document.querySelectorAll(".importe-actual");

  const ahora = Math.floor(Date.now() / 1000); // Obtener el tiempo actual en segundos

  elementosTiempo.forEach(elemento => {
    const horaIngreso = parseInt(elemento.getAttribute("data-ingreso"));
    let minutosTranscurridos = Math.floor((ahora - horaIngreso) / 60); // Convertir a minutos

    let horas = Math.floor(minutosTranscurridos / 60);
    let minutos = minutosTranscurridos % 60;
    elemento.innerText = `${horas}h ${minutos}m`;
  });

  elementosCosto.forEach(elemento => {
    const horaIngreso = parseInt(elemento.getAttribute("data-ingreso"));
    const costoPorMinuto = parseFloat(elemento.getAttribute("data-costo-por-minuto"));
    let minutosTranscurridos = Math.floor((ahora - horaIngreso) / 60);

    let costo = 0;
    if (minutosTranscurridos > 15) {
      let minutosCobrados = minutosTranscurridos - 15; // Restar los 15 min de tolerancia
      costo = minutosCobrados * costoPorMinuto;
      costo = Math.floor(costo / 100) * 100; // Redondear hacia abajo en múltiplos de 100
    }

    elemento.innerText = `$${costo.toLocaleString('en-US')}`;
  });
}

// Ejecutar la función cada segundo para actualizar el tiempo y el costo dinámicamente
setInterval(actualizarTiempoYCosto, 1000);

// Llamar la función una vez al inicio para evitar esperar el primer intervalo
actualizarTiempoYCosto();


//funcion para editar vehiculo
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  const searchForm = searchInput.closest('form');
  const clearSearchBtn = document.getElementById('clearSearchBtn');
  const tipoVehiculoSelect = document.getElementById('tipoVehiculo');
  const ordenSelector = document.getElementById('ordenSelector');
  const hiddenTipoVehiculo = document.getElementById('hiddenTipoVehiculo');
  const hiddenOrden = document.getElementById('hiddenOrden');
  let timeoutId;

  // Inicializar botones de edición
  document.querySelectorAll('.editar-ticket').forEach(function(button) {
      button.addEventListener('click', function() {
          const idVehiculo = this.getAttribute('data-id-vehiculo');
          const idRegistro = this.getAttribute('data-id');
          const placa = this.getAttribute('data-placa');
          const tipo = this.getAttribute('data-tipo');
          const descripcionVehiculo = this.getAttribute('data-descripcion');
          
          console.log('Datos del vehículo:', {
              idVehiculo,
              idRegistro,
              placa,
              tipo,
              descripcionVehiculo
          });
          
          // Llenar el formulario de edición
          document.getElementById('editar_id_vehiculo').value = idVehiculo;
          document.getElementById('editar_id_registro').value = idRegistro;
          document.getElementById('editar_placa').value = placa;
          
          // Seleccionar el tipo de vehículo correcto
          const tipoSelect = document.getElementById('editar_tipo');
          for (let i = 0; i < tipoSelect.options.length; i++) {
              if (tipoSelect.options[i].value === tipo.toLowerCase()) {
                  tipoSelect.selectedIndex = i;
                  break;
              }
          }
          
          // Establecer la descripción del vehículo
          document.getElementById('editar_descripcion').value = descripcionVehiculo || '';
          
          // Almacenar los valores originales para comparar
          const form = document.getElementById('formEdicionVehiculo');
          form.setAttribute('data-original-placa', placa);
          form.setAttribute('data-original-tipo', tipo.toLowerCase());
          form.setAttribute('data-original-descripcion', descripcionVehiculo || '');
          
          // Mostrar el modal
          const modal = new bootstrap.Modal(document.getElementById('modalEdicion'));
          modal.show();
      });
  });
  
  // Confirmar cierre del modal si hay cambios sin guardar
  document.querySelector('#modalEdicion .btn-close, #modalEdicion .btn-secondary').addEventListener('click', function(e) {
      const form = document.getElementById('formEdicionVehiculo');
      const placaOriginal = form.getAttribute('data-original-placa');
      const tipoOriginal = form.getAttribute('data-original-tipo');
      const descripcionOriginal = form.getAttribute('data-original-descripcion');
      
      const placaActual = document.getElementById('editar_placa').value;
      const tipoActual = document.getElementById('editar_tipo').value;
      const descripcionActual = document.getElementById('editar_descripcion').value;
      
  });

  // Evento para búsqueda automática al escribir
  searchInput.addEventListener('input', function() {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(function() {
          searchForm.submit();
      }, 800); // Esperar 800ms después de que el usuario deje de escribir
  });

  // Evento para el select de tipo de vehículo
  if (tipoVehiculoSelect) {
      tipoVehiculoSelect.addEventListener('change', function() {
          hiddenTipoVehiculo.value = tipoVehiculoSelect.value;
          searchForm.submit();
      });
  }

  // Evento para el selector de orden
  if (ordenSelector) {
      ordenSelector.addEventListener('change', function() {
          hiddenOrden.value = ordenSelector.value;
          searchForm.submit();
      });
  }

  // Evento para el botón de limpiar búsqueda
  if (clearSearchBtn) {
      clearSearchBtn.addEventListener('click', function() {
          searchInput.value = ''; // Limpiar el campo de búsqueda
          hiddenTipoVehiculo.value = ''; // Limpiar el tipo de vehículo
          hiddenOrden.value = 'desc'; // Restablecer orden a predeterminado
          
          // Actualizar los selectores visibles
          if (tipoVehiculoSelect) tipoVehiculoSelect.value = '';
          if (ordenSelector) ordenSelector.value = 'desc';
          
          searchForm.submit(); // Enviar el formulario para mostrar todos los tickets
      });
  }
  
  // Recargar la página sin parámetro 'actualizado' después de una edición exitosa
  if (new URLSearchParams(window.location.search).has('actualizado')) {
      // Eliminar el parámetro 'actualizado' de la URL
      let newUrl = window.location.href.replace(/[?&]actualizado=1/, '');
      // Si la URL termina con '?' después de eliminar el parámetro, eliminar también ese '?'
      if (newUrl.endsWith('?')) {
          newUrl = newUrl.slice(0, -1);
      }
      // Esperar un segundo para que el usuario pueda ver el mensaje de éxito
      setTimeout(function() {
          // Mostrar efecto de recarga
          document.querySelector('.tickets-container').style.opacity = '0.5';
          // Recargar la página sin el parámetro 'actualizado'
          window.location.href = newUrl;
      }, 1000);
  }
});
// Funcion para mostrar datos del ticket seleccionado y tomar datos para cerrar ticket
document.addEventListener("DOMContentLoaded", function () {
  const botonesCerrar = document.querySelectorAll(".cerrar-ticket");
  let intervaloCosto = null; // Para almacenar el intervalo y poder detenerlo

  botonesCerrar.forEach(boton => {
    boton.addEventListener("click", function () {
      const ticketId = this.getAttribute("data-id");
      const horaIngreso = parseInt(this.getAttribute("data-ingreso"));
      const costoPorMinuto = parseFloat(this.getAttribute("data-costo-por-minuto"));
      const placa = this.getAttribute("data-placa");
      const tipo = this.getAttribute("data-tipo");
      const metodoPago = this.getAttribute("data-metodo-pago");

      // Formatear la hora de ingreso a "dd/mm HH:MM"
      const fechaIngreso = new Date(horaIngreso * 1000);
      const horaIngresoFormateada = fechaIngreso.toLocaleString("es-CO", {
        day: "2-digit",
        month: "2-digit",
        hour: "2-digit",
        minute: "2-digit"
      });

      // Función para actualizar tiempo transcurrido y costo en el modal
      function actualizarTiempoYCostoModal() {
        const ahora = Math.floor(Date.now() / 1000); // Tiempo actual en segundos
        let minutosTranscurridos = Math.floor((ahora - horaIngreso) / 60);

        // Formatear tiempo transcurrido en "Xh Ym"
        let horas = Math.floor(minutosTranscurridos / 60);
        let minutos = minutosTranscurridos % 60;
        let tiempoTranscurrido = `${horas}h ${minutos}m`;

        let costo = 0;
        if (minutosTranscurridos > 15) {
          let minutosCobrados = minutosTranscurridos - 15; // Restar los 15 min de tolerancia
          costo = minutosCobrados * costoPorMinuto;
          costo = Math.floor(costo / 100) * 100; // Redondear en múltiplos de 100
        }

        // Calcular los items del cobro
        let cantidadHoras = Math.floor(minutosTranscurridos / 60);
        let cantidadMinutos = minutosTranscurridos % 60;
        let costoHoras = cantidadHoras * 2000;
        let costoMinutos = (cantidadMinutos > 15) ? (cantidadMinutos - 15) * costoPorMinuto : 0;

        // Obtener el valor actual del input (si ya fue editado, conservar el texto del usuario)
        let descripcionActual = document.getElementById("modalDescripcion").value;
        let nuevaDescripcion = `#${placa}`; // Solo mostrar la placa del vehículo

        // Si el usuario no ha editado la descripción, actualizarla automáticamente
        if (!descripcionActual) {
          document.getElementById("modalDescripcion").value = nuevaDescripcion;
        }
        // Mostrar datos en el modal
        document.getElementById("ticketInfo").innerHTML = `
                #${placa} • ${tipo} <br>
                <small>Ingreso: ${horaIngresoFormateada}</small> <br>
                <small>Tiempo: ${tiempoTranscurrido}</small>
            `;

        //datos los cuales seran enviados al formulario para cerrar el ticket
        document.getElementById("modalCosto").innerText = `$${costo.toLocaleString('en-US')}`;
        document.getElementById("total_pagado").value = `${costo.toLocaleString().replace(/\./g, '')}`;
        document.getElementById("id_ticket").value = `${ticketId.toLocaleString()}`;
        // Mostrar el detalle en "Items"
        document.getElementById("modalItems").value =
          `${cantidadHoras} x Hora ($2000) + ${cantidadMinutos} min ($${Math.floor(costoMinutos)}) = $${costo}`;
      }

      // Llamar la función inmediatamente y actualizar cada segundo
      actualizarTiempoYCostoModal();
      if (intervaloCosto) clearInterval(intervaloCosto);
      intervaloCosto = setInterval(actualizarTiempoYCostoModal, 1000);

      // Mostrar el modal
      const modal = new bootstrap.Modal(document.getElementById("modalPago"));
      modal.show();

      // Detener la actualización cuando el modal se cierre
      document.getElementById("modalPago").addEventListener("hidden.bs.modal", function () {
        clearInterval(intervaloCosto);
      });
    });
  });
});

//funcion para la busqueda de tickets
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  const searchForm = searchInput.closest('form');
  const clearSearchBtn = document.getElementById('clearSearchBtn');
  const tipoVehiculoSelect = document.getElementById('tipoVehiculo');
  const ordenSelector = document.getElementById('ordenSelector');
  const hiddenTipoVehiculo = document.getElementById('hiddenTipoVehiculo');
  const hiddenOrden = document.getElementById('hiddenOrden');
  let timeoutId;

  // Evento para búsqueda automática al escribir
  searchInput.addEventListener('input', function() {
      clearTimeout(timeoutId);
      timeoutId = setTimeout(function() {
          searchForm.submit();
      }, 800); // Esperar 800ms después de que el usuario deje de escribir
  });

  // Evento para el select de tipo de vehículo
  if (tipoVehiculoSelect) {
      tipoVehiculoSelect.addEventListener('change', function() {
          hiddenTipoVehiculo.value = tipoVehiculoSelect.value;
          searchForm.submit();
      });
  }

  // Evento para el selector de orden
  if (ordenSelector) {
      ordenSelector.addEventListener('change', function() {
          hiddenOrden.value = ordenSelector.value;
          searchForm.submit();
      });
  }

  // Evento para el botón de limpiar búsqueda
  if (clearSearchBtn) {
      clearSearchBtn.addEventListener('click', function() {
          searchInput.value = ''; // Limpiar el campo de búsqueda
          hiddenTipoVehiculo.value = ''; // Limpiar el tipo de vehículo
          hiddenOrden.value = 'desc'; // Restablecer orden a predeterminado
          
          // Actualizar los selectores visibles
          if (tipoVehiculoSelect) tipoVehiculoSelect.value = '';
          if (ordenSelector) ordenSelector.value = 'desc';
          
          searchForm.submit(); // Enviar el formulario para mostrar todos los tickets
      });
  }
});

//navegador
document.addEventListener("DOMContentLoaded", function () {
  const tabs = document.querySelectorAll(".nav-link");
  const contents = document.querySelectorAll(".tab-content");

  // Comprobar si hay un parámetro tab en la URL
  const urlParams = new URLSearchParams(window.location.search);
  const tabParam = urlParams.get('tab');
  
  if (tabParam) {
    // Si hay un parámetro tab, activar esa pestaña
    tabs.forEach(t => t.classList.remove("active"));
    contents.forEach(content => content.classList.add("d-none"));
    
    // Activar la pestaña correspondiente
    const selectedTab = document.querySelector(`.nav-link[data-tab="${tabParam}"]`);
    if (selectedTab) {
      selectedTab.classList.add("active");
      document.getElementById(tabParam).classList.remove("d-none");
    }
  }

  tabs.forEach(tab => {
    tab.addEventListener("click", function (event) {
      if (!this.getAttribute('href') || this.getAttribute('href') === '#') {
        event.preventDefault();

        // Obtener el ID de la pestaña
        const tabId = this.getAttribute("data-tab");
        
        // Actualizar la URL sin recargar la página
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tabId);
        window.history.pushState({}, '', url);

        // Quitar la clase "active" de todas las pestañas
        tabs.forEach(t => t.classList.remove("active"));
        // Ocultar todos los contenidos
        contents.forEach(content => content.classList.add("d-none"));

        // Agregar "active" a la pestaña seleccionada
        this.classList.add("active");
        // Mostrar el contenido correspondiente
        document.getElementById(tabId).classList.remove("d-none");
      }
    });
  });
});

font_change("Public-Sans");
preset_change("preset-1");
layout_rtl_change('false');
layout_change('light');

// Función para cancelar tickets
document.addEventListener('DOMContentLoaded', function() {
  // Seleccionar todos los botones de cancelar
  const botonesCancelar = document.querySelectorAll('.cancelar-ticket');
  
  botonesCancelar.forEach(boton => {
    boton.addEventListener('click', function() {
      const ticketId = this.getAttribute('data-id');
      const placa = this.getAttribute('data-placa');
      
      // Llenar los campos en el modal de cancelación
      document.getElementById('cancelar_id_registro').value = ticketId;
      document.getElementById('cancelar_placa').textContent = placa;
      
      // Mostrar el modal de cancelación
      const modal = new bootstrap.Modal(document.getElementById('modalCancelacion'));
      modal.show();
    });
  });
});