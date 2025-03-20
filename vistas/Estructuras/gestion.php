<?php
session_start();
include '../../modelo/conexion.php';
include '../../controladores/seguridad.php';
include '../../controladores/consultas_tap1.php';
date_default_timezone_set('America/Bogota'); // Cambia 'America/Bogota' por tu zona horaria

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
  <!-- [Font Awesome CSS] -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
      <div class="mt-1">
        <!-- Menú de Navegación -->
        <ul class="nav nav-tabs justify-content-center" id="menu-tabs">
          <li class="nav-item">
            <a class="nav-link active" data-tab="tab1" href="#">Tickets Abiertos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab2" href="#">Tickets Cerrados</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab3" href="#">Entradas / Salidas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab4" href="#">Reportes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-tab="tab5" href="#">Estadísticas</a>
          </li>
        </ul>

        <!-- Contenido dinámico -->
        <div class="tab-container mt-4">
          <!-- tap 1 tickets abiertos -->
          <?php include 'gestion_tap/tap1.php'; ?>
          <!-- tap 2 tickets cerrados -->
          <?php include 'gestion_tap/tap2.php'; ?>
          <!-- tap 2 tickets cerrados -->
          <?php include 'gestion_tap/tap3.php'; ?>

          <div id="tab4" class="tab-content d-none">
            <h3>Reportes</h3>
            <p>Genera y visualiza reportes detallados sobre los tickets y actividades.</p>
          </div>
          <div id="tab5" class="tab-content d-none">
            <h3>Estadísticas</h3>
            <p>Sección de análisis y estadísticas sobre el rendimiento del sistema.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->
  <!-- [ Footer ] start -->
  <?php include 'layouts/footer.php'; ?>
  <script>

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
  <script src="../assets/js/ticket.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const botonesCerrar = document.querySelectorAll(".cerrar-ticket");
      let intervaloCosto = null; // Para almacenar el intervalo y poder detenerlo

      botonesCerrar.forEach(boton => {
        boton.addEventListener("click", function() {
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
          document.getElementById("modalPago").addEventListener("hidden.bs.modal", function() {
            clearInterval(intervaloCosto);
          });
        });
      });
    });
  </script>
</body>
<!-- [Body] end -->

</html>