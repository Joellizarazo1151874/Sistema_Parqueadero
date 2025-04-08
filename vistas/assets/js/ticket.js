//funcion para actualizar la hora y el importe del cliente constantemente en pantalla
function actualizarTiempoYCosto() {
  const elementosTiempo = document.querySelectorAll(".tiempo-transcurrido");
  const elementosCosto = document.querySelectorAll(".importe-actual");
  const elementosDebe = document.querySelectorAll(".total-debe");

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
    const toleranciaMinutos = parseInt(elemento.getAttribute("data-tolerancia") || "15"); // Valor por defecto: 15
    const tarifaHora = parseFloat(elemento.getAttribute("data-tarifa-hora") || "0");
    const tarifaDia = parseFloat(elemento.getAttribute("data-tarifa-dia") || "0");
    let minutosTranscurridos = Math.floor((ahora - horaIngreso) / 60);

    // Calcular el costo según la tarifa aplicable
    let costo = 0;
    
    // Calcular horas y minutos transcurridos
    let horasCompletas = Math.floor(minutosTranscurridos / 60);
    let minutosRestantes = minutosTranscurridos % 60;
    
    // Si es tarifa por hora, calcular correctamente
    if (tarifaHora > 0) {
      // Costo por horas completas
      costo = horasCompletas * tarifaHora;
      
      // Añadir costo por minutos (solo si superan la tolerancia)
      if (minutosRestantes > toleranciaMinutos) {
        costo += (minutosRestantes - toleranciaMinutos) * costoPorMinuto;
      }
    } 
    // Si hay tarifa por día, calcular por días
    else if (tarifaDia > 0) {
      // Calcular días transcurridos
      let diasCompletos = Math.ceil(minutosTranscurridos / (24 * 60));
      
      // Si no ha pasado la tolerancia, no se cobra nada
      if (minutosTranscurridos <= toleranciaMinutos) {
        diasCompletos = 0;
      }
      
      costo = diasCompletos * tarifaDia;
    }
    // Si no hay tarifa por hora ni día pero hay tolerancia y costo por minuto
    else if (toleranciaMinutos > 0 && costoPorMinuto > 0) {
      // Si los minutos superan la tolerancia, cobrar los minutos adicionales
      if (minutosTranscurridos > toleranciaMinutos) {
        costo = (minutosTranscurridos - toleranciaMinutos) * costoPorMinuto;
      }
    }
    
    // Redondear hacia abajo en múltiplos de 100
    costo = Math.floor(costo / 100) * 100;

    elemento.innerText = `$${costo.toLocaleString('es-CO')}`;
  });

  // Actualizar elementos "DEBE" (total-debe)
  elementosDebe.forEach(elemento => {
    const horaIngreso = parseInt(elemento.getAttribute("data-ingreso"));
    const costoPorMinuto = parseFloat(elemento.getAttribute("data-costo-por-minuto"));
    const toleranciaMinutos = parseInt(elemento.getAttribute("data-tolerancia") || "15");
    const tarifaHora = parseFloat(elemento.getAttribute("data-tarifa-hora") || "0");
    const tarifaDia = parseFloat(elemento.getAttribute("data-tarifa-dia") || "0");
    const totalCostos = parseFloat(elemento.getAttribute("data-total-costos") || "0");
    let minutosTranscurridos = Math.floor((ahora - horaIngreso) / 60);

    // Calcular el costo según la tarifa aplicable
    let costo = 0;
    
    // Calcular horas y minutos transcurridos
    let horasCompletas = Math.floor(minutosTranscurridos / 60);
    let minutosRestantes = minutosTranscurridos % 60;
    
    // Si es tarifa por hora, calcular correctamente
    if (tarifaHora > 0) {
      // Costo por horas completas
      costo = horasCompletas * tarifaHora;
      
      // Añadir costo por minutos (solo si superan la tolerancia)
      if (minutosRestantes > toleranciaMinutos) {
        costo += (minutosRestantes - toleranciaMinutos) * costoPorMinuto;
      }
    } 
    // Si hay tarifa por día, calcular por días
    else if (tarifaDia > 0) {
      // Calcular días transcurridos
      let diasCompletos = Math.ceil(minutosTranscurridos / (24 * 60));
      
      // Si no ha pasado la tolerancia, no se cobra nada
      if (minutosTranscurridos <= toleranciaMinutos) {
        diasCompletos = 0;
      }
      
      costo = diasCompletos * tarifaDia;
    }
    // Si no hay tarifa por hora ni día pero hay tolerancia y costo por minuto
    else if (toleranciaMinutos > 0 && costoPorMinuto > 0) {
      // Si los minutos superan la tolerancia, cobrar los minutos adicionales
      if (minutosTranscurridos > toleranciaMinutos) {
        costo = (minutosTranscurridos - toleranciaMinutos) * costoPorMinuto;
      }
    }
    
    // Redondear hacia abajo en múltiplos de 100
    costo = Math.floor(costo / 100) * 100;
    
    // Sumar los costos adicionales al importe actual
    const totalPagar = costo + totalCostos;
    
    // Mostrar el total a pagar
    elemento.innerText = `$${totalPagar.toLocaleString('es-CO')}`;
  });
}

// Ejecutar la función cada segundo para actualizar el tiempo y el costo dinámicamente
setInterval(actualizarTiempoYCosto, 1000);

// Llamar la función una vez al inicio para evitar esperar el primer intervalo
actualizarTiempoYCosto();

// Añadir información de depuración para verificar cálculos
document.addEventListener('DOMContentLoaded', function() {
  console.log("=== Inicializando sistema de cálculo de costos ===");
  
  // Mostrar cómo se están calculando los precios para depuración
  const tickets = document.querySelectorAll('.ticket');
  tickets.forEach((ticket, index) => {
    const importeActual = ticket.querySelector('.importe-actual');
    const cierreBTN = ticket.querySelector('.cerrar-ticket');
    
    if (importeActual && cierreBTN) {
      console.log(`Ticket #${index + 1} - Datos de cálculo:`, {
        tipo_registro: cierreBTN.getAttribute('data-tipo-registro') || cierreBTN.getAttribute('data-tolerancia-tipo') || 'hora',
        tarifa_hora: parseFloat(importeActual.getAttribute('data-tarifa-hora') || '0'),
        tarifa_dia: parseFloat(importeActual.getAttribute('data-tarifa-dia') || '0'),
        costo_por_minuto: parseFloat(importeActual.getAttribute('data-costo-por-minuto') || '0'),
        tolerancia: parseInt(importeActual.getAttribute('data-tolerancia') || '15')
      });
    }
  });
});

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
      const toleranciaMinutos = parseInt(this.getAttribute("data-tolerancia") || "15");
      const tarifaHora = parseFloat(this.getAttribute("data-tarifa-hora") || "0");
      const tarifaDia = parseFloat(this.getAttribute("data-tarifa-dia") || "0");
      const tarifaValor = parseFloat(this.getAttribute("data-tarifa-valor") || "0");
      const placa = this.getAttribute("data-placa");
      const tipo = this.getAttribute("data-tipo");
      const tipoRegistro = this.getAttribute("data-tipo-registro") || this.getAttribute("data-tolerancia-tipo") || "hora"; // Obtener el tipo de registro
      const tiempoHoras = parseFloat(this.getAttribute("data-tiempo-horas") || "1"); // Obtener el tiempo en horas configurado
      const metodoPago = this.getAttribute("data-metodo-pago");

      console.log('Datos del ticket a cerrar:', {
        id: ticketId,
        placa: placa,
        tipo: tipo,
        tipoRegistro: tipoRegistro,
        tiempoHoras: tiempoHoras,
        tarifas: {
          hora: tarifaHora,
          dia: tarifaDia,
          valor: tarifaValor
        }
      });

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

        let costoItem = 0;
        if (minutosTranscurridos > toleranciaMinutos) {
          let minutosCobrados = minutosTranscurridos - toleranciaMinutos; // Restar los min de tolerancia
          costoItem = minutosCobrados * costoPorMinuto;
          costoItem = Math.floor(costoItem / 100) * 100; // Redondear en múltiplos de 100
        }

        // Calcular los items del cobro según el tipo de registro
        let cantidadHoras = Math.floor(minutosTranscurridos / 60);
        let cantidadMinutos = minutosTranscurridos % 60;
        let detalleCobro = '';
        
        // Ajustar el detalle según el tipo de registro y el tiempo configurado
        if (tipoRegistro === 'hora') {
          // Calcular costo por horas completas
          costoItem = cantidadHoras * tarifaHora;
          
          // Calcular costo por minutos adicionales (solo si superan la tolerancia)
          let costoMinutos = 0;
          if (cantidadMinutos > toleranciaMinutos) {
            costoMinutos = (cantidadMinutos - toleranciaMinutos) * costoPorMinuto;
          }
          
          // Sumar ambos costos
          costoItem += costoMinutos;
          
          // Redondear a múltiplos de 100
          costoItem = Math.floor(costoItem / 100) * 100;
          
          detalleCobro = `${cantidadHoras} x Hora ($${tarifaHora.toLocaleString()}) + ${Math.max(0, cantidadMinutos - toleranciaMinutos)} min ($${Math.floor(costoMinutos)})`;
        } else {
          // Para cualquier tipo de tarifa que no sea hora, usar el tiempo configurado
          // Convertir minutos transcurridos a horas
          let horasTranscurridas = minutosTranscurridos / 60;
          
          // Si no ha pasado la tolerancia, no se cobra nada
          if (minutosTranscurridos <= toleranciaMinutos) {
            costoItem = 0;
            detalleCobro = `Dentro de tolerancia (${toleranciaMinutos} min)`;
          } else {
            // Calcular proporción del periodo transcurrido
            let proporcion = Math.min(horasTranscurridas / tiempoHoras, 1);
            
            // Si ha pasado más tiempo que el configurado, calcular periodos completos y la proporción adicional
            if (horasTranscurridas > tiempoHoras) {
              let periodosCompletos = Math.floor(horasTranscurridas / tiempoHoras);
              let horasRestantes = horasTranscurridas % tiempoHoras;
              let proporcionRestante = horasRestantes / tiempoHoras;
              
              // Cobrar periodos completos más la proporción adicional
              costoItem = (periodosCompletos + proporcionRestante) * tarifaValor;
              
              // Formatear el detalle según el tipo de periodo
              if (tipoRegistro === 'dia') {
                detalleCobro = `${periodosCompletos} día(s) y ${(horasRestantes).toFixed(1)} horas ($${tarifaValor.toLocaleString()})`;
              } else if (tipoRegistro === 'semana') {
                detalleCobro = `${periodosCompletos} semana(s) y ${(horasRestantes / 24).toFixed(1)} días ($${tarifaValor.toLocaleString()})`;
              } else if (tipoRegistro === 'mes') {
                detalleCobro = `${periodosCompletos} mes(es) y ${(horasRestantes / 24).toFixed(1)} días ($${tarifaValor.toLocaleString()})`;
              } else if (tipoRegistro === 'año' || tipoRegistro === 'anio') {
                detalleCobro = `${periodosCompletos} año(s) y ${(horasRestantes / 24).toFixed(1)} días ($${tarifaValor.toLocaleString()})`;
              } else {
                detalleCobro = `${periodosCompletos} x ${tipoRegistro} y fracción (${(proporcionRestante * 100).toFixed(1)}%) ($${tarifaValor.toLocaleString()})`;
              }
            } else {
              // Si es menos que el tiempo configurado, cobrar la proporción
              costoItem = proporcion * tarifaValor;
              
              // Formatear el detalle según el tipo de periodo
              if (tipoRegistro === 'dia') {
                detalleCobro = `${horasTranscurridas.toFixed(1)} horas de día ($${tarifaValor.toLocaleString()})`;
              } else if (tipoRegistro === 'semana') {
                detalleCobro = `${(horasTranscurridas / 24).toFixed(1)} días de semana ($${tarifaValor.toLocaleString()})`;
              } else if (tipoRegistro === 'mes') {
                detalleCobro = `${(horasTranscurridas / 24).toFixed(1)} días de mes ($${tarifaValor.toLocaleString()})`;
              } else if (tipoRegistro === 'año' || tipoRegistro === 'anio') {
                detalleCobro = `${(horasTranscurridas / 24).toFixed(1)} días de ${tipoRegistro === 'año' ? 'año' : 'anio'} ($${tarifaValor.toLocaleString()})`;
              } else {
                detalleCobro = `${(proporcion * 100).toFixed(1)}% de ${tipoRegistro} ($${tarifaValor.toLocaleString()})`;
              }
            }
          }
          
          // Redondear a múltiplos de 100
          costoItem = Math.floor(costoItem / 100) * 100;
        }

        // Usar costoItem calculado para el total a pagar
        costoItem = costoItem;

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
                <small>Tiempo: ${tiempoTranscurrido}</small> <br>
                <small>Tipo: ${tipoRegistro.charAt(0).toUpperCase() + tipoRegistro.slice(1)} (${tiempoHoras}h)</small>
            `;

        //datos los cuales seran enviados al formulario para cerrar el ticket
        document.getElementById("modalCosto").innerText = `$${costoItem.toLocaleString('en-US')}`;
        document.getElementById("total_pagado").value = `${costoItem.toLocaleString().replace(/\./g, '')}`;
        document.getElementById("id_ticket").value = `${ticketId.toLocaleString()}`;
        // Mostrar el detalle en "Items"
        document.getElementById("modalItems").value = `${detalleCobro} = $${costoItem}`;
        
        // Después de establecer los valores base, obtener y sumar los costos adicionales
        fetch(`../../controladores/obtener_costos_adicionales.php?id_registro=${ticketId}`)
          .then(response => response.json())
          .then(data => {
            if (data.costos && data.costos.length > 0) {
              const totalAdicionales = parseFloat(data.total);
              const totalFinal = costoItem + totalAdicionales;
              
              // Actualizar el valor mostrado con el total (estacionamiento + adicionales)
              document.getElementById("modalCosto").innerText = `$${totalFinal.toLocaleString('en-US')}`;
              document.getElementById("total_pagado").value = `${totalFinal}`;
              
              // Actualizar el detalle en "Items" para incluir los adicionales
              document.getElementById("modalItems").value = `${detalleCobro} = $${costoItem.toLocaleString('en-US')} + Adicionales $${totalAdicionales.toLocaleString('en-US')}`;
            }
          })
          .catch(error => {
            console.error('Error al actualizar costos adicionales:', error);
          });
      }

      // Llamar la función inmediatamente y actualizar cada 5 segundos en lugar de cada segundo
      actualizarTiempoYCostoModal();
      if (intervaloCosto) clearInterval(intervaloCosto);
      intervaloCosto = setInterval(actualizarTiempoYCostoModal, 5000);

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

// Función para cargar los costos adicionales en el modal de pago
function cargarCostosAdicionalesPago(idRegistro) {
  fetch(`../../controladores/obtener_costos_adicionales.php?id_registro=${idRegistro}`)
    .then(response => response.json())
    .then(data => {
      const costosContainer = document.getElementById('costos_adicionales_pago');
      const listaContainer = document.getElementById('lista_costos_adicionales');
      const totalElement = document.getElementById('total_costos_adicionales_pago');
      
      // Si no hay costos adicionales, ocultar la sección
      if (!data.costos || data.costos.length === 0) {
        costosContainer.classList.add('d-none');
        return;
      }
      
      // Mostrar la sección de costos adicionales
      costosContainer.classList.remove('d-none');
      
      // Limpiar contenedor
      listaContainer.innerHTML = '';
      
      // Crear lista de costos
      const ul = document.createElement('ul');
      ul.className = 'list-group list-group-flush mb-2';
      
      // Añadir cada costo a la lista
      data.costos.forEach(costo => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.innerHTML = `
          <span>${costo.concepto}</span>
          <span>$${parseFloat(costo.valor).toLocaleString('es-CO')}</span>
        `;
        ul.appendChild(li);
      });
      
      listaContainer.appendChild(ul);
      
      // Actualizar el total de costos adicionales
      totalElement.textContent = `$${parseFloat(data.total).toLocaleString('es-CO')}`;
      
      // Ya no sumamos los costos adicionales aquí porque ya se suman en actualizarTiempoYCostoModal
    })
    .catch(error => {
      console.error('Error al cargar los costos adicionales:', error);
      document.getElementById('costos_adicionales_pago').classList.add('d-none');
    });
}

//funcion para la busqueda de tickets
// ... existing code ...