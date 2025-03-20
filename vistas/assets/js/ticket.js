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

    elemento.innerText = `$${costo.toLocaleString()}`;
  });
}

// Ejecutar la función cada segundo para actualizar el tiempo y el costo dinámicamente
setInterval(actualizarTiempoYCosto, 1000);

// Llamar la función una vez al inicio para evitar esperar el primer intervalo
actualizarTiempoYCosto();

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
        let nuevaDescripcion = `Ticket #${placa} • ${tipo} • Inicio: ${horaIngresoFormateada} • Permanencia: ${tiempoTranscurrido}`;

        // Si el usuario no ha editado la descripción, actualizarla automáticamente
        if (!descripcionActual || descripcionActual.startsWith("Ticket #")) {
          document.getElementById("modalDescripcion").value = nuevaDescripcion;
        }
        // Mostrar datos en el modal
        document.getElementById("ticketInfo").innerHTML = `
                #${placa} • ${tipo} <br>
                <small>Ingreso: ${horaIngresoFormateada}</small> <br>
                <small>Tiempo: ${tiempoTranscurrido}</small>
            `;

        //datos los cuales seran enviados al formulario para cerrar el ticket
        document.getElementById("modalCosto").innerText = `$${costo.toLocaleString()}`;
        document.getElementById("total_pagado").value = `${costo.toLocaleString()}`;
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


//navegador
document.addEventListener("DOMContentLoaded", function () {
  const tabs = document.querySelectorAll(".nav-link");
  const contents = document.querySelectorAll(".tab-content");

  tabs.forEach(tab => {
    tab.addEventListener("click", function (event) {
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

font_change("Public-Sans");
preset_change("preset-1");
layout_rtl_change('false');
layout_change('light');