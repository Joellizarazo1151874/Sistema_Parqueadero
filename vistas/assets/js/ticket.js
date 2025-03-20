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



  //navegador
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

  font_change("Public-Sans");
  preset_change("preset-1");
  layout_rtl_change('false');
  layout_change('light');