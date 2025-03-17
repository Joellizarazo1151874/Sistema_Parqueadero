
function actualizarTiempoTranscurrido() {
    const elementos = document.querySelectorAll(".tiempo-transcurrido");

    elementos.forEach(elemento => {
        const horaIngreso = parseInt(elemento.getAttribute("data-ingreso")) * 1000; // Convertir a milisegundos
        const ahora = new Date().getTime(); // Tiempo actual en milisegundos
        const diferencia = ahora - horaIngreso; // Diferencia en milisegundos

        // Convertir a horas y minutos
        const horas = Math.floor(diferencia / (1000 * 60 * 60));
        const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));

        // Mostrar el tiempo transcurrido
        elemento.innerText = `${horas}h ${minutos}m`;
    });
}

// Actualizar cada segundo
setInterval(actualizarTiempoTranscurrido, 1000);

// Llamar la función una vez al inicio
actualizarTiempoTranscurrido();
