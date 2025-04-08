<?php
// Configurar la zona horaria para Colombia
date_default_timezone_set('America/Bogota');

// Incluir conexión a la base de datos
include '../modelo/conexion.php';

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Error: ID de recibo no proporcionado";
    exit;
}

$id_recibo = intval($_GET['id']);

// Consultar los detalles del recibo
$sql = "SELECT r.*, v.placa, v.tipo as tipo_vehiculo, mp.nombre as nombre_metodo_pago
        FROM registros_parqueo r 
        LEFT JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
        LEFT JOIN metodos_pago mp ON r.metodo_pago = mp.id_metodo
        WHERE r.id_registro = ? AND r.estado = 'cerrado'";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_recibo);
$stmt->execute();
$resultado = $stmt->get_result();

if (!$resultado || $resultado->num_rows == 0) {
    echo "Error: Recibo no encontrado o no está cerrado";
    exit;
}

$recibo = $resultado->fetch_assoc();

// Calcular tiempo de estancia
$hora_ingreso = new DateTime($recibo['hora_ingreso']);
$hora_salida = new DateTime($recibo['hora_salida']);
$diferencia = $hora_ingreso->diff($hora_salida);

// Formatear tiempo de estancia
$tiempo_estancia = '';
if ($diferencia->days > 0) {
    $tiempo_estancia .= $diferencia->days . 'd ';
}
$tiempo_estancia .= $diferencia->h . 'h ' . $diferencia->i . 'm';

// Formatear fechas para mostrar
$hora_ingreso_formateada = $hora_ingreso->format('d/m/Y H:i');
$hora_salida_formateada = $hora_salida->format('d/m/Y H:i');

// Obtener costos adicionales
$sql_costos = "SELECT * FROM costos_adicionales WHERE id_registro = ?";
$stmt_costos = $conexion->prepare($sql_costos);
$stmt_costos->bind_param("i", $id_recibo);
$stmt_costos->execute();
$resultado_costos = $stmt_costos->get_result();

$costos_adicionales = [];
$total_adicionales = 0;

if ($resultado_costos && $resultado_costos->num_rows > 0) {
    while ($costo = $resultado_costos->fetch_assoc()) {
        $costos_adicionales[] = [
            'concepto' => $costo['concepto'],
            'valor' => $costo['valor']
        ];
        $total_adicionales += floatval($costo['valor']);
    }
}

// Calcular costo de estacionamiento (total - adicionales)
$costo_estacionamiento = floatval($recibo['total_pagado']) - $total_adicionales;

// Formatear el total pagado y otros montos según las preferencias del usuario
$total_pagado_formateado = '$' . number_format($recibo['total_pagado'], 0, '', ',');
$costo_estacionamiento_formateado = '$' . number_format($costo_estacionamiento, 0, '', ',');
$total_adicionales_formateado = '$' . number_format($total_adicionales, 0, '', ',');

// Generar el HTML para imprimir
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?php echo $id_recibo; ?> - SmartPark</title>
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
        .total {
            font-size: 12pt;
            font-weight: bold;
            text-align: right;
            margin-top: 3mm;
            padding-top: 2mm;
            border-top: 1px dashed #000;
        }
        .detalle-factura {
            margin: 3mm 0;
            border: 1px solid #000;
            padding: 2mm;
            background-color: #f9f9f9;
        }
        .detalle-factura .titulo {
            text-align: center;
            font-weight: bold;
            margin-bottom: 2mm;
            border-bottom: 1px solid #000;
            padding-bottom: 1mm;
        }
        .footer {
            margin-top: 5mm;
            text-align: center;
            font-size: 9pt;
            border-top: 1px dashed #000;
            padding-top: 2mm;
        }
        .barcode {
            text-align: center;
            margin: 3mm 0;
        }
        .barcode img {
            max-width: 100%;
            height: auto;
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
</head>
<body>
    <div class="ticket-container">
        <div class="header">
            <h1>SMARTPARK</h1>
            <p>SISTEMA DE PARQUEADERO</p>
            <p>COMPROBANTE DE PAGO</p>
            <p>TICKET #<?php echo $id_recibo; ?></p>
            <p><?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
        
        <div class="info-item">
            <span class="label">PLACA:</span>
            <span class="value"><?php echo htmlspecialchars($recibo['placa']); ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">TIPO DE TARIFA:</span>
            <span class="value"><?php echo htmlspecialchars($recibo['tipo']); ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">TIPO DE VEHÍCULO:</span>
            <span class="value"><?php echo htmlspecialchars($recibo['tipo_vehiculo']); ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">ENTRADA:</span>
            <span class="value"><?php echo $hora_ingreso_formateada; ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">SALIDA:</span>
            <span class="value"><?php echo $hora_salida_formateada; ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">TIEMPO:</span>
            <span class="value"><?php echo $tiempo_estancia; ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">MÉTODO PAGO:</span>
            <span class="value"><?php echo htmlspecialchars($recibo['nombre_metodo_pago'] ?? $recibo['metodo_pago']); ?></span>
        </div>
        
        <div class="separator"></div>
        
        <!-- Detalle de factura -->

            <div class="titulo" style="text-align: center;">Detalle de factura</div>
            
            <div class="info-item">
                <span class="label">Estacionamiento</span>
                <span class="value"><?php echo $costo_estacionamiento_formateado; ?></span>
            </div>
            
            <?php if (count($costos_adicionales) > 0): ?>
                <?php foreach ($costos_adicionales as $costo): ?>
                <div class="info-item">
                    <span class="label"><?php echo htmlspecialchars($costo['concepto']); ?></span>
                    <span class="value">$<?php echo number_format($costo['valor'], 0, '', ','); ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="separator"></div>
            
            <div class="info-item">
                <span class="label"><strong>TOTAL</strong></span>
                <span class="value"><strong><?php echo $total_pagado_formateado; ?></strong></span>
            </div>
     
        
        <div class="separator"></div>
        
        <!-- Código de barras simple (podría ser reemplazado por un código QR) -->
        <div class="barcode">
            <div>*<?php echo str_pad($id_recibo, 10, '0', STR_PAD_LEFT); ?>*</div>
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
        // Imprimir automáticamente al cargar la página
        window.onload = function() {
            // Esperar un momento para que se cargue todo correctamente
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
<?php
$stmt->close();
$stmt_costos->close();
$conexion->close();
?>
