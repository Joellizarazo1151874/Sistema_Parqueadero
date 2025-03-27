# DOCUMENTACIÓN TÉCNICA: SISTEMA DE GESTIÓN DE PARQUEADERO "SMARTPARK"

## 1. INTRODUCCIÓN

### 1.1 Propósito del documento
Este documento proporciona una descripción detallada del sistema de gestión de parqueadero "SmartPark", incluyendo su arquitectura, funcionalidades, modelo de datos y guías de uso. Está dirigido a desarrolladores, administradores del sistema y personal técnico responsable del mantenimiento y actualización del software.

### 1.2 Alcance del sistema
SmartPark es una aplicación web diseñada para administrar y controlar el flujo de vehículos en un estacionamiento, permitiendo el registro de entradas y salidas, cálculo automático de tarifas, emisión de tickets y generación de reportes. El sistema está orientado a negocios de parqueaderos públicos o privados que requieren una solución integral para la gestión de sus operaciones diarias.

### 1.3 Definiciones y abreviaturas
- **Ticket**: Registro digital que representa la estancia de un vehículo en el parqueadero.
- **Ticket Activo**: Vehículo que se encuentra actualmente en el parqueadero.
- **Ticket Cerrado**: Vehículo que ha salido del parqueadero y ha completado su pago.
- **Tiempo de tolerancia**: Período inicial (15 minutos) durante el cual no se cobra al usuario.

## 2. ARQUITECTURA DEL SISTEMA

### 2.1 Visión general
SmartPark está desarrollado como una aplicación web utilizando PHP para el backend, MySQL como base de datos, y HTML/CSS/JavaScript para el frontend. Sigue una arquitectura de tres capas:

1. **Capa de Presentación**: Interfaces de usuario (vistas)
2. **Capa de Lógica de Negocio**: Controladores PHP
3. **Capa de Datos**: Modelo de datos y conexión a la base de datos MySQL

### 2.2 Estructura de directorios
```
Parqueadero/
├── controladores/   # Lógica de negocio y procesamiento de solicitudes
│   ├── check.php
│   ├── cierre_ticket.php
│   ├── consultas_tap1.php
│   ├── seguridad.php
│   └── ticket.php
├── modelo/          # Conexión a la base de datos y operaciones CRUD
│   └── conexion.php
└── vistas/          # Interfaces de usuario
    ├── assets/      # Recursos estáticos (JS, CSS, imágenes)
    │   ├── css/
    │   ├── fonts/
    │   ├── images/
    │   └── js/
    │       └── ticket.js
    └── Estructuras/ # Componentes de la interfaz
        ├── gestion.php
        ├── gestion_tap/
        │   ├── tap1.php  # Tickets Abiertos
        │   ├── tap2.php  # Tickets Cerrados
        │   └── tap3.php  # Entradas/Salidas
        └── layouts/
            ├── footer.php
            ├── header.php
            └── menu.php
```

### 2.3 Tecnologías utilizadas
- **Backend**: PHP 7.4+
- **Base de datos**: MySQL 5.7+
- **Frontend**: 
  - HTML5, CSS3, JavaScript
  - Bootstrap 5.3.0
  - Font Awesome 5.15.4
  - jQuery
- **Complementos**:
  - ApexCharts (para gráficos y visualización de datos)
  - Feather Icons
  - Tabler Icons

## 3. MODELO DE DATOS

### 3.1 Diagrama entidad-relación
El sistema utiliza principalmente las siguientes tablas:

1. **vehiculos**
   - id_vehiculo (PK)
   - placa
   - tipo (moto, auto, camioneta, etc.)
   - otros_atributos

2. **registros_parqueo**
   - id_registro (PK)
   - id_vehiculo (FK)
   - hora_ingreso
   - hora_salida
   - estado (activo, cerrado)
   - total_pagado
   - metodo_pago
   - descripcion

3. **usuarios**
   - id_usuario (PK)
   - nombre
   - usuario
   - contrasena
   - rol
   - otros_atributos

### 3.2 Relaciones
- Un vehículo puede tener múltiples registros de parqueo (1:N)
- Un usuario puede registrar múltiples tickets (1:N)

## 4. FUNCIONALIDADES IMPLEMENTADAS

### 4.1 Gestión de tickets
- **Registro de entrada**: Creación de tickets nuevos con información del vehículo.
- **Visualización de tickets activos**: Interfaz para ver todos los vehículos actualmente en el parqueadero.
- **Cierre de tickets**: Proceso para registrar la salida de vehículos y calcular el importe a pagar.
- **Historial de tickets cerrados**: Consulta de tickets anteriores con filtros por fecha, tipo de vehículo, etc.

### 4.2 Cálculo automático de tarifas
- **Configuración de tarifas**: Tarifa base de 2000 pesos por hora.
- **Tiempo de tolerancia**: 15 minutos iniciales sin costo.
- **Cálculo en tiempo real**: Actualización automática del importe a medida que transcurre el tiempo.
- **Redondeo de tarifas**: Redondeo hacia arriba para cobrar horas completas después del tiempo de tolerancia.

### 4.3 Gestión de pagos
- **Múltiples métodos de pago**: Efectivo, tarjeta de crédito, tarjeta de débito, MercadoPago.
- **Comprobantes**: Opción para emitir o no un comprobante de pago.
- **Registro de transacciones**: Almacenamiento de información detallada sobre cada pago.

### 4.4 Reportes y estadísticas
- **Entradas/Salidas**: Registro cronológico de movimientos en el parqueadero.
- **Reportes financieros**: Análisis de ingresos por períodos.
- **Estadísticas de uso**: Análisis de ocupación y rotación de vehículos.

## 5. PROCESOS PRINCIPALES

### 5.1 Flujo de registro de entrada
1. Captura de datos del vehículo (placa, tipo)
2. Registro de hora de ingreso
3. Creación de ticket con estado "activo"
4. Visualización del ticket en la interfaz de tickets abiertos

### 5.2 Flujo de cierre de ticket
1. Selección del ticket a cerrar desde la interfaz de tickets abiertos
2. Cálculo automático del importe según tiempo transcurrido y tarifas establecidas
3. Selección del método de pago
4. Confirmación del pago y registro de datos adicionales
5. Actualización del estado del ticket a "cerrado"
6. Registro de la hora de salida
7. Actualización de la interfaz (eliminación del ticket de la vista de tickets abiertos)

### 5.3 Cálculo de tarifas
```
Si minutos_transcurridos <= 15:
    importe = 0
Si minutos_transcurridos > 15:
    horas_a_cobrar = ceil((minutos_transcurridos - 15) / 60)
    importe = horas_a_cobrar * 2000
```

## 6. ASPECTOS TÉCNICOS

### 6.1 Gestión de sesiones
El sistema utiliza la gestión de sesiones de PHP para mantener el estado de los usuarios autenticados y controlar el acceso a las diferentes funcionalidades.

### 6.2 Seguridad
- **Autenticación**: Control de acceso mediante usuario y contraseña.
- **Control de sesiones**: Validación de sesiones activas.
- **Sanitización de datos**: Protección contra inyección SQL y ataques XSS.
- **Validación de entradas**: Verificación de tipos y rangos de datos ingresados.

### 6.3 Manejo de zonas horarias
El sistema está configurado para trabajar con la zona horaria "America/Bogota", asegurando que todos los cálculos de tiempo y tarifas sean precisos según la ubicación del negocio.

### 6.4 Actualizaciones en tiempo real
El sistema utiliza JavaScript para actualizar automáticamente la información de tiempo transcurrido e importe a cobrar, con intervalos de actualización configurados para optimizar el rendimiento.

## 7. GUÍA DE USO

### 7.1 Interfaz principal
La interfaz principal se divide en cinco pestañas:
1. **Tickets Abiertos**: Visualización y gestión de vehículos actualmente en el parqueadero.
2. **Tickets Cerrados**: Historial de tickets finalizados con detalles de pago.
3. **Entradas/Salidas**: Registro cronológico de movimientos.
4. **Reportes**: Generación de informes personalizados.
5. **Estadísticas**: Visualización de métricas y análisis de datos.

### 7.2 Cerrar un ticket
1. En la pestaña "Tickets Abiertos", localice el ticket deseado.
2. Haga clic en el botón "Cerrar" asociado al ticket.
3. En el modal que aparece, verifique la información del vehículo y el importe calculado.
4. Seleccione el método de pago en el menú desplegable.
5. Complete la descripción si es necesario.
6. Seleccione la opción de comprobante (con o sin).
7. Haga clic en "Cobrar" para finalizar el proceso.

### 7.3 Búsqueda de tickets
Utilice el campo de búsqueda en la parte superior de la interfaz para filtrar tickets por placa de vehículo.

## 8. MANTENIMIENTO Y ACTUALIZACIÓN

### 8.1 Respaldo de datos
Se recomienda realizar copias de seguridad periódicas de la base de datos para prevenir pérdida de información.

### 8.2 Monitoreo de rendimiento
Es importante supervisar el tamaño de las tablas de la base de datos, especialmente la tabla `registros_parqueo`, que crecerá constantemente con el uso del sistema.

### 8.3 Optimización
Para mejorar el rendimiento en instalaciones con gran volumen de tickets, considere:
- Implementar paginación en las vistas de tickets
- Crear índices adicionales en la base de datos
- Archivar registros antiguos

## 9. APÉNDICES

### 9.1 Requisitos del sistema
- Servidor web con soporte para PHP 7.4+
- Base de datos MySQL 5.7+ o MariaDB
- Navegador web moderno (Chrome, Firefox, Edge, Safari)
- Conexión a Internet para cargar recursos externos (CDN)


---

Documentación elaborada: [Fecha actual]
Versión del sistema: 1.0
