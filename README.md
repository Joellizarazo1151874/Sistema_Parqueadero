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
- **Ticket Cancelado**: Vehículo que ha salido del parqueadero sin completar un pago.
- **Tiempo de tolerancia**: Período inicial configurable durante el cual no se cobra al usuario.

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
│   ├── actualizar_tarifa.php
│   ├── agregar_categoria.php
│   ├── agregar_cliente.php
│   ├── agregar_tolerancia.php
│   ├── agregar_usuario.php
│   ├── cancelar_ticket.php
│   ├── cerrar_sesion.php
│   ├── check.php
│   ├── cierre_ticket.php
│   ├── consultas_*.php  # Varios archivos de consultas
│   ├── editar_*.php     # Varios archivos de edición
│   ├── eliminar_*.php   # Varios archivos de eliminación
│   ├── obtener_*.php    # Varios archivos de obtención de datos
│   ├── registro_parqueo.php
│   ├── seguridad.php
│   └── verificar_estructura_tarifas.php
├── modelo/          # Conexión a la base de datos y operaciones CRUD
│   ├── conexion_pdo.php
│   └── conexion.php
├── vistas/          # Interfaces de usuario
│   ├── assets/      # Recursos estáticos (JS, CSS, imágenes)
│   ├── Estructuras/ # Componentes de la interfaz
│   │   ├── configuracion_tap/
│   │   ├── gestion_tap/
│   │   ├── clientes_tap/
│   │   ├── layouts/
│   │   ├── caja.php
│   │   ├── clientes.php
│   │   ├── configuracion.php
│   │   └── gestion.php
│   └── formularios/ # Formularios de ingreso y registro
├── ginuss_smartpark.sql # Archivo de estructura de la base de datos
├── index.php       # Punto de entrada principal
└── README.md       # Documentación
```

### 2.3 Tecnologías utilizadas
- **Backend**: PHP 8.2.12
- **Base de datos**: MariaDB 10.4.32
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
![mermaid-diagram-2025-04-04-150146](https://github.com/user-attachments/assets/33955a3f-5b3b-485f-8e5c-327d73653360)

### 3.1 Tablas principales
El sistema utiliza las siguientes tablas principales:

1. **clientes**
   - id_cliente (PK)
   - nombre
   - telefono
   - correo
   - fecha_registro

2. **vehiculos**
   - id_vehiculo (PK)
   - placa
   - tipo (moto, auto, camioneta, etc.)
   - fecha_registro

3. **registros_parqueo**
   - id_registro (PK)
   - id_vehiculo (FK)
   - hora_ingreso
   - hora_salida
   - estado (activo, cerrado, cancelado)
   - total_pagado
   - metodo_pago
   - descripcion
   - cerrado_por
   - abierto_por
   - tipo (hora, dia, mes, año, etc.)
   - tiempo_horas

4. **usuarios**
   - id_usuario (PK)
   - nombre
   - usuario
   - contrasena
   - rol

5. **tarifas**
   - id_tarifa (PK)
   - tipo_vehiculo
   - hora
   - dia
   - mes
   - año
   - fecha_actualizacion

6. **tolerancia**
   - id_tolerancia (PK)
   - tiempo_minutos
   - fecha_actualizacion

### 3.2 Tablas adicionales
1. **incidentes**
   - id_incidente (PK)
   - id_cliente (FK)
   - id_registro (FK)
   - tipo
   - descripcion
   - evidencia
   - fecha_registro

2. **pagos**
   - id_pago (PK)
   - id_registro (FK)
   - id_suscripcion (FK)
   - monto
   - metodo
   - fecha_pago

3. **reportes_caja**
   - id_reporte (PK)
   - fecha
   - total_ingresos
   - efectivo
   - transferencia
   - fecha_generacion

4. **suscripciones**
   - id_suscripcion (PK)
   - id_cliente (FK)
   - id_vehiculo (FK)
   - fecha_inicio
   - fecha_fin
   - monto_pagado
   - estado

### 3.3 Relaciones
- Un vehículo puede tener múltiples registros de parqueo (1:N)
- Un cliente puede tener múltiples vehículos (1:N)
- Un cliente puede tener múltiples suscripciones (1:N)
- Un usuario puede registrar múltiples tickets (1:N)

## 4. FUNCIONALIDADES IMPLEMENTADAS

### 4.1 Gestión de tickets
- **Registro de entrada**: Creación de tickets nuevos con información del vehículo.
- **Visualización de tickets activos**: Interfaz para ver todos los vehículos actualmente en el parqueadero.
- **Cierre de tickets**: Proceso para registrar la salida de vehículos y calcular el importe a pagar.
- **Cancelación de tickets**: Opción para cancelar tickets sin cobro.
- **Historial de tickets cerrados**: Consulta de tickets anteriores con filtros por fecha, tipo de vehículo, etc.

### 4.2 Gestión de tarifas flexibles
- **Tarifas por tipo de vehículo**: Configuración de tarifas específicas según el tipo de vehículo.
- **Múltiples modalidades**: Tarifas por hora, día, semana, mes, año y períodos personalizados (4 horas, 8 horas).
- **Tiempo de tolerancia configurable**: Ajuste del período inicial sin costo.
- **Actualización de tarifas**: Panel administrativo para modificar las tarifas en cualquier momento.

### 4.3 Gestión de pagos
- **Múltiples métodos de pago**: Efectivo, tarjeta de crédito, tarjeta de débito, MercadoPago.
- **Registro de transacciones**: Almacenamiento de información detallada sobre cada pago.
- **Anotaciones**: Posibilidad de agregar notas o descripciones a cada transacción.

### 4.4 Gestión de clientes
- **Registro de clientes**: Alta de clientes frecuentes o con membresías.
- **Edición y eliminación**: Mantenimiento de la base de datos de clientes.
- **Asociación con vehículos**: Vinculación de clientes con sus vehículos.

### 4.5 Administración de usuarios
- **Gestión de usuarios**: Creación, edición y eliminación de usuarios del sistema.
- **Control de acceso**: Asignación de roles y permisos.
- **Trazabilidad**: Registro de qué usuario abrió o cerró cada ticket.

### 4.6 Configuración del sistema
- **Panel de ajustes**: Interfaz para configurar parámetros globales del sistema.
- **Personalización de tolerancias**: Ajuste de los tiempos de gracia.
- **Gestión de categorías de vehículos**: Administración de los tipos de vehículos y sus tarifas.

## 5. PROCESOS PRINCIPALES

### 5.1 Flujo de registro de entrada
1. Captura de datos del vehículo (placa, tipo)
2. Registro de hora de ingreso
3. Selección del tipo de tarifa (hora, día, semana, mes, año, períodos personalizados)
4. Creación de ticket con estado "activo"
5. Visualización del ticket en la interfaz de tickets abiertos

### 5.2 Flujo de cierre de ticket
1. Selección del ticket a cerrar desde la interfaz de tickets abiertos
2. Cálculo automático del importe según tiempo transcurrido, tipo de tarifa y tarifas establecidas
3. Selección del método de pago
4. Confirmación del pago y registro de datos adicionales
5. Actualización del estado del ticket a "cerrado"
6. Registro de la hora de salida y del usuario que realizó el cierre
7. Actualización de la interfaz

### 5.3 Flujo de cancelación de ticket
1. Selección del ticket a cancelar desde la interfaz de tickets abiertos
2. Ingreso del motivo de cancelación
3. Confirmación de la cancelación
4. Actualización del estado del ticket a "cancelado"
5. Registro de la hora de cancelación y del usuario que realizó la cancelación

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

### 6.4 Comunicación cliente-servidor
El sistema utiliza JavaScript y AJAX para realizar actualizaciones en tiempo real sin recargar la página, mejorando la experiencia del usuario y proporcionando datos actualizados constantemente.

## 7. GUÍA DE USO

### 7.1 Interfaz principal
La interfaz principal se divide en varias secciones:
1. **Gestión**: Manejo de tickets activos y cerrados, registro de entradas/salidas.
   - **Tickets Abiertos**: Visualización y gestión de vehículos actualmente en el parqueadero.
   - **Tickets Cerrados**: Historial de tickets finalizados con detalles de pago.
   - **Entradas/Salidas**: Registro cronológico de movimientos.
2. **Clientes**: Administración de la base de datos de clientes.
3. **Caja**: Gestión financiera y cierres de caja.
4. **Configuración**: Ajustes del sistema, tarifas, tolerancias y gestión de usuarios.

### 7.2 Registrar entrada de vehículo
1. Acceda a la pestaña "Tickets Abiertos" en la sección "Gestión".
2. Ingrese la placa del vehículo.
3. Seleccione el tipo de vehículo.
4. Elija el tipo de tarifa (hora, día, semana, mes, año, períodos personalizados).
5. Haga clic en "Registrar Entrada".

### 7.3 Cerrar un ticket
1. En la pestaña "Tickets Abiertos", localice el ticket deseado.
2. Haga clic en el botón "Cerrar" asociado al ticket.
3. En el modal que aparece, verifique la información del vehículo y el importe calculado.
4. Seleccione el método de pago en el menú desplegable.
5. Complete la descripción si es necesario.
6. Haga clic en "Cobrar" para finalizar el proceso.

### 7.4 Cancelar un ticket
1. En la pestaña "Tickets Abiertos", localice el ticket deseado.
2. Haga clic en el botón "Cancelar" asociado al ticket.
3. Ingrese el motivo de la cancelación.
4. Confirme la cancelación.

### 7.5 Configurar tarifas
1. Acceda a la sección "Configuración".
2. Seleccione "Tarifas" en el menú lateral.
3. Modifique las tarifas por tipo de vehículo y modalidad.
4. Guarde los cambios.

### 7.6 Gestionar clientes
1. Acceda a la sección "Clientes".
2. Para agregar un cliente, complete el formulario y haga clic en "Agregar Cliente".
3. Para editar o eliminar un cliente, utilice los botones correspondientes en la lista de clientes.

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

## 9. PRÓXIMAS MEJORAS PLANIFICADAS

### 9.1 Funcionalidades futuras
- **Módulo de suscripciones**: Implementación completa del sistema de membresías.
- **Módulo de incidentes**: Gestión de eventos y situaciones especiales.
- **Aplicación móvil**: Desarrollo de app para clientes.
- **Integración con sistemas de acceso automático**: Conexión con barreras automáticas.

## 10. APÉNDICES

### 10.1 Requisitos del sistema
- Servidor web con soporte para PHP 8.2+
- Base de datos MariaDB 10.4+ o MySQL 5.7+
- Navegador web moderno (Chrome, Firefox, Edge, Safari)
- Conexión a Internet para cargar recursos externos (CDN)

---

Documentación actualizada: Abril 2025
Versión del sistema: 1.5
