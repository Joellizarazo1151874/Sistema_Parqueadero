document.addEventListener('DOMContentLoaded', function() {
    // Verificar si estamos en la página de caja
    const tabCaja = document.getElementById('tab1');
    if (!tabCaja) return;

    // Elementos del DOM
    const btnCerrarTickets = document.querySelectorAll('.cerrar-ticket');
    const btnVerTickets = document.querySelectorAll('.ver-ticket');
    const btnLimpiarFiltrosAbiertos = document.getElementById('limpiarFiltrosAbiertos');
    const modalCerrarTicket = document.getElementById('modalCerrarTicket');
    const formCerrarTicket = document.getElementById('formCerrarTicket');
    const idRegistroCerrar = document.getElementById('id_registro_cerrar');
    const infoTicket = document.getElementById('info_ticket');
    const btnConfirmarCierre = document.getElementById('btnConfirmarCierre');

    // Evento para limpiar filtros de tickets abiertos
    if (btnLimpiarFiltrosAbiertos) {
        btnLimpiarFiltrosAbiertos.addEventListener('click', function() {
            // Redirigir a la página sin parámetros de búsqueda
            window.location.href = 'caja.php?tab=tab1';
        });
    }

    // Evento para abrir modal de cierre de ticket
    btnCerrarTickets.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const idRegistro = this.getAttribute('data-id');
            abrirModalCerrarTicket(idRegistro);
        });
    });

    // Evento para ver detalles del ticket
    btnVerTickets.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const idRegistro = this.getAttribute('data-id');
            verDetallesTicket(idRegistro);
        });
    });

    // Evento para confirmar cierre de ticket
    if (btnConfirmarCierre) {
        btnConfirmarCierre.addEventListener('click', function() {
            if (formCerrarTicket.checkValidity()) {
                confirmarCierreTicket();
            } else {
                // Mostrar validación de formulario
                formCerrarTicket.classList.add('was-validated');
            }
        });
    }

    // Función para abrir modal de cierre de ticket
    function abrirModalCerrarTicket(idRegistro) {
        if (!idRegistro) return;

        // Limpiar información previa
        if (infoTicket) {
            infoTicket.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando información...</div>';
        }

        // Establecer el ID del registro en el formulario
        if (idRegistroCerrar) {
            idRegistroCerrar.value = idRegistro;
        }

        // Abrir el modal
        const modal = new bootstrap.Modal(modalCerrarTicket);
        modal.show();

        // Cargar información del ticket
        fetch(`../../controladores/obtener_info_ticket.php?id=${idRegistro}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al obtener información del ticket');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Calcular tiempo transcurrido
                    const horaIngreso = new Date(data.ticket.hora_ingreso);
                    const horaActual = new Date();
                    const diferenciaMilisegundos = horaActual - horaIngreso;
                    
                    // Convertir milisegundos a horas, minutos
                    const horas = Math.floor(diferenciaMilisegundos / (1000 * 60 * 60));
                    const minutos = Math.floor((diferenciaMilisegundos % (1000 * 60 * 60)) / (1000 * 60));
                    
                    // Formatear tiempo transcurrido
                    let tiempoTranscurrido = '';
                    if (horas > 24) {
                        const dias = Math.floor(horas / 24);
                        tiempoTranscurrido = `${dias}d ${horas % 24}h ${minutos}m`;
                    } else {
                        tiempoTranscurrido = `${horas}h ${minutos}m`;
                    }
                    
                    // Generar un ID de ticket aleatorio (similar al formato del ejemplo)
                    const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    let ticketId = '';
                    for (let i = 0; i < 6; i++) {
                        ticketId += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
                    }
                    
                    // Crear descripción del ticket en el formato deseado
                    const fechaIngreso = new Date(data.ticket.hora_ingreso);
                    const dia = fechaIngreso.getDate();
                    const mes = fechaIngreso.getMonth() + 1;
                    const hora = fechaIngreso.getHours();
                    const minuto = fechaIngreso.getMinutes();
                    const ampm = hora >= 12 ? 'p. m.' : 'a. m.';
                    const hora12 = hora % 12 || 12;
                    
                    const descripcion = `Ticket #${ticketId} • ${data.ticket.tipo_vehiculo.toUpperCase()} • Inicio: ${dia}/${mes}, ${hora12}:${minuto.toString().padStart(2, '0')} ${ampm} • ${data.ticket.placa.toUpperCase()}`;
                    
                    // Establecer la descripción en el campo correspondiente
                    const descripcionTicket = document.getElementById('descripcion_ticket');
                    if (descripcionTicket) {
                        descripcionTicket.value = descripcion;
                    }
                    
                    // Mostrar información en el modal
                    if (infoTicket) {
                        infoTicket.innerHTML = `
                            <div class="row">
                                <div class="col-6"><strong>Placa:</strong></div>
                                <div class="col-6">${data.ticket.placa.toUpperCase()}</div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6"><strong>Tipo:</strong></div>
                                <div class="col-6">${data.ticket.tipo_vehiculo.toUpperCase()}</div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6"><strong>Ingreso:</strong></div>
                                <div class="col-6">${new Date(data.ticket.hora_ingreso).toLocaleString('es-CO')}</div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6"><strong>Tiempo:</strong></div>
                                <div class="col-6">${tiempoTranscurrido}</div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6"><strong>Tarifa:</strong></div>
                                <div class="col-6">$${data.ticket.valor_hora_formateado}/hora</div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6"><strong>Total a pagar:</strong></div>
                                <div class="col-6 fw-bold">$${data.ticket.total_estimado_formateado}</div>
                            </div>
                        `;
                    }
                } else {
                    // Mostrar mensaje de error
                    if (infoTicket) {
                        infoTicket.innerHTML = `<div class="alert alert-danger">Error: ${data.message}</div>`;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (infoTicket) {
                    infoTicket.innerHTML = `<div class="alert alert-danger">Error al cargar la información del ticket. Por favor, inténtelo de nuevo.</div>`;
                }
            });
    }

    // Función para ver detalles del ticket
    function verDetallesTicket(idRegistro) {
        if (!idRegistro) return;

        // Mostrar detalles en un modal bonito usando SweetAlert
        fetch(`../../controladores/obtener_info_ticket.php?id=${idRegistro}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al obtener información del ticket');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Calcular tiempo transcurrido
                    const horaIngreso = new Date(data.ticket.hora_ingreso);
                    const horaActual = new Date();
                    const diferenciaMilisegundos = horaActual - horaIngreso;
                    
                    // Convertir milisegundos a horas, minutos
                    const horas = Math.floor(diferenciaMilisegundos / (1000 * 60 * 60));
                    const minutos = Math.floor((diferenciaMilisegundos % (1000 * 60 * 60)) / (1000 * 60));
                    
                    // Formatear tiempo transcurrido
                    let tiempoTranscurrido = '';
                    if (horas > 24) {
                        const dias = Math.floor(horas / 24);
                        tiempoTranscurrido = `${dias}d ${horas % 24}h ${minutos}m`;
                    } else {
                        tiempoTranscurrido = `${horas}h ${minutos}m`;
                    }
                    
                    // Mostrar información en un modal
                    Swal.fire({
                        title: `Ticket #${data.ticket.id_registro}`,
                        html: `
                            <div class="text-start">
                                <div class="row">
                                    <div class="col-6"><strong>Placa:</strong></div>
                                    <div class="col-6">${data.ticket.placa}</div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-6"><strong>Tipo:</strong></div>
                                    <div class="col-6">${data.ticket.tipo_vehiculo}</div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-6"><strong>Ingreso:</strong></div>
                                    <div class="col-6">${new Date(data.ticket.hora_ingreso).toLocaleString('es-CO')}</div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-6"><strong>Tiempo:</strong></div>
                                    <div class="col-6">${tiempoTranscurrido}</div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-6"><strong>Tarifa:</strong></div>
                                    <div class="col-6">$${data.ticket.valor_hora_formateado}/hora</div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-6"><strong>Total a pagar:</strong></div>
                                    <div class="col-6 fw-bold">$${data.ticket.total_estimado_formateado}</div>
                                </div>
                            </div>
                        `,
                        icon: 'info',
                        confirmButtonText: 'Cerrar',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: true
                    });
                } else {
                    // Mostrar mensaje de error
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'Cerrar'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Error al cargar la información del ticket. Por favor, inténtelo de nuevo.',
                    icon: 'error',
                    confirmButtonText: 'Cerrar'
                });
            });
    }

    // Función para confirmar el cierre del ticket
    function confirmarCierreTicket() {
        // Verificar que se haya seleccionado un método de pago
        const metodoPago = document.getElementById('metodo_pago');
        if (!metodoPago || !metodoPago.value) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe seleccionar un método de pago',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        // Mostrar confirmación
        Swal.fire({
            title: '¿Confirmar pago?',
            text: 'Esta acción cerrará el ticket y registrará el pago',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Enviar formulario
                document.getElementById('formCerrarTicket').submit();
            }
        });
    }
});
