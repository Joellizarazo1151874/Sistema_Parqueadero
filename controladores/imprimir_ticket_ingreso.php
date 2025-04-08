<?php
// Configurar la zona horaria para Colombia
date_default_timezone_set('America/Bogota');

// Incluir conexión a la base de datos
include '../modelo/conexion.php';

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Error: ID de ticket no proporcionado";
    exit;
}

$id_registro = intval($_GET['id']);

// Consultar los detalles del ticket
$sql = "SELECT r.*, v.placa, v.tipo as tipo_vehiculo 
        FROM registros_parqueo r 
        LEFT JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
        WHERE r.id_registro = ? AND r.estado = 'activo'";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_registro);
$stmt->execute();
$resultado = $stmt->get_result();

if (!$resultado || $resultado->num_rows == 0) {
    echo "Error: Ticket no encontrado o no está activo";
    exit;
}

$ticket = $resultado->fetch_assoc();

// Obtener fecha y hora de ingreso
$hora_ingreso = new DateTime($ticket['hora_ingreso']);
$fecha_ingreso_formateada = $hora_ingreso->format('d/m/Y');
$hora_ingreso_formateada = $hora_ingreso->format('H:i');

// Generar el URL para el código QR
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$qr_data = $base_url . "/Parqueadero/controladores/escanear_ticket.php?id=" . $id_registro;

// Generar el HTML para imprimir
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Ingreso #<?php echo $id_registro; ?> - SmartPark</title>
    <style>
        @page {
            size: 80mm 297mm; /* Ancho típico de tickets térmicos (80mm) */
            margin: 0;
        }
        body {
            font-family: 'Courier New', monospace; /* Fuente típica para tickets */
            margin: 0;
            padding: 5mm;
            font-size: 10pt;
            width: 70mm; /* Ancho del contenido */
            margin: 0 auto;
        }
        .ticket-container {
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 5mm;
            border-bottom: 1px dashed #000;
            padding-bottom: 2mm;
        }
        .header h1 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
        }
        .header p {
            margin: 1mm 0;
        }
        .info-item {
            margin-bottom: 1mm;
            display: flex;
            justify-content: space-between;
        }
        .info-item .label {
            font-weight: bold;
        }
        .separator {
            border-top: 1px dashed #000;
            margin: 3mm 0;
        }
        .footer {
            margin-top: 5mm;
            text-align: center;
            font-size: 9pt;
            border-top: 1px dashed #000;
            padding-top: 2mm;
        }
        .qrcode {
            text-align: center;
            margin: 3mm auto;
            width: auto;
        }
        .qrcode svg {
            width: 150px;
            height: 150px;
        }
        .warning {
            text-align: center;
            font-weight: bold;
            margin: 3mm 0;
            font-size: 8pt;
        }
        .no-print {
            display: none;
        }
        @media print {
            html, body {
                width: 80mm;
                height: auto;
            }
            .ticket-container {
                width: 70mm;
            }
            .no-print {
                display: none;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
</head>
<body>
    <div class="ticket-container">
        <div class="header">
            <h1>SMARTPARK</h1>
            <p>SISTEMA DE PARQUEADERO</p>
            <p>TICKET DE INGRESO</p>
            <p>TICKET #<?php echo $id_registro; ?></p>
            <p><?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
        
        <div class="info-item">
            <span class="label">PLACA:</span>
            <span class="value"><?php echo htmlspecialchars($ticket['placa']); ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">TIPO DE VEHÍCULO:</span>
            <span class="value"><?php echo htmlspecialchars($ticket['tipo_vehiculo']); ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">FECHA DE INGRESO:</span>
            <span class="value"><?php echo $fecha_ingreso_formateada; ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">HORA DE INGRESO:</span>
            <span class="value"><?php echo $hora_ingreso_formateada; ?></span>
        </div>
        
        <div class="separator"></div>
        
        <div class="qrcode">
            <canvas id="qrcode"></canvas>
        </div>
        
        <div class="warning">
            POR FAVOR, CONSERVE ESTE TICKET.<br>
            NECESARIO PARA RETIRAR SU VEHÍCULO.
        </div>
        
        <div class="footer">
            <p>GRACIAS POR SU VISITA</p>
            <p>VUELVA PRONTO</p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print();" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Imprimir Ticket
        </button>
        <button onclick="window.close();" style="padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            Cerrar
        </button>
    </div>
    
    <script>
        // Generar código QR con JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            var qr = new QRious({
                element: document.getElementById('qrcode'),
                value: '<?php echo $qr_data; ?>',
                size: 200,
                level: 'H'
            });
        });
        
        // Imprimir automáticamente al cargar la página
        window.onload = function() {
            // Esperar un momento para que se cargue todo correctamente
            setTimeout(function() {
                window.print();
            }, 1000); // Aumentado a 1 segundo para dar tiempo a que se genere el QR
        };
    </script>
</body>
</html>
<?php
$stmt->close();
$conexion->close();
?> 