/**
 * Sistema de cámara y reconocimiento de placas persistente
 * Este script permite que la cámara siga funcionando mientras se navega por la aplicación
 */

// Variables globales para la cámara
let cameraStream = null;
let recognitionActive = false;
let floatingWindowActive = true;
let minimized = false;
let minimizedPrevHeight = null; // Para guardar la altura previa
let plateRecognitionTimer = null;
let lastDetectedPlate = '';
let simulationMode = true;
let isDragging = false;
let dragStartPos = { x: 0, y: 0 };

// Exponer las variables a nivel de window para que sean accesibles entre navegaciones
window.cameraStream = null; // Se actualizará con la referencia real
window.cameraState = {
    active: false,
    initialized: false,
    position: {
        left: null,
        top: null,
        right: '20px',
        bottom: '20px'
    },
    size: {
        width: '256px',
        height: '224px'
    }
};

// Elemento principal de la cámara
let cameraContainer = null;
let cameraWindow = null;
let cameraHeader = null;
let plateAlert = null;

// Estado actual de la cámara
const cameraState = {
    active: false,
    initialized: false
};

// Comunicación con el Service Worker
function setupServiceWorkerCommunication() {
    if ('serviceWorker' in navigator) {
        // Escuchar mensajes del Service Worker
        navigator.serviceWorker.addEventListener('message', event => {
            if (event.data) {
                console.log('[Camera System] Mensaje recibido del Service Worker:', event.data);
                
                if (event.data.type === 'CHECK_CAMERA') {
                    // Verificar estado de la cámara y restaurarla si es necesario
                    checkAndRestoreCamera();
                } else if (event.data.type === 'REACTIVATE_CAMERA') {
                    // Forzar reactivación de la cámara
                    if (!cameraStream && window.cameraState.active) {
                        startCamera();
                    }
                }
            }
        });
        
        // Notificar al Service Worker que la cámara está activa
        function notifyCameraActive() {
            if (navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage({
                    type: 'CAMERA_ACTIVE',
                    active: true
                });
            }
        }
        
        // Configurar para notificar periódicamente
        setInterval(() => {
            if (cameraStream && cameraState.active) {
                notifyCameraActive();
            }
        }, 10000);
    }
}

// Inicialización al cargar
document.addEventListener('DOMContentLoaded', function() {
    console.log('Iniciando sistema de cámara persistente...');
    
    // Verificar si ya está inicializado (para evitar duplicados en recargas)
    if (document.getElementById('global-camera-container')) {
        console.log('Sistema de cámara ya inicializado');
        
        // Si la cámara estaba activa pero el stream se perdió, reiniciarla
        if (window.cameraState && window.cameraState.active && (!cameraStream || cameraStream.getTracks().length === 0)) {
            console.log('Detectada cámara activa sin stream. Reiniciando...');
            setTimeout(() => {
                startCamera();
            }, 500);
        }
        return;
    }

    // Crear elementos globales
    createCameraElements();
    
    // Inicializar sistema
    initCameraSystem();
    
    // Verificar si la cámara estaba activa anteriormente
    const savedState = localStorage.getItem('cameraState');
    if (savedState) {
        const state = JSON.parse(savedState);
        console.log('Recuperando estado de cámara:', state);
        
        // Restaurar estado visual
        restoreCameraState(state);
        
        // Si estaba activa, iniciarla automáticamente
        if (state.active && !cameraStream) {
            setTimeout(() => {
                startCamera();
            }, 1000);
        }
    }
    
    // Configurar eventos
    setupEventListeners();
    
    // Configurar eventos de visibilidad para mantener la cámara activa
    setupVisibilityEvents();
    
    // Comunicación con el Service Worker
    setupServiceWorkerCommunication();
    
    // Verificar si tenemos que restaurar la cámara desde otra página
    if (localStorage.getItem('cameraWasActive') === 'true') {
        console.log('Restaurando cámara desde navegación anterior...');
        setTimeout(() => {
            startCamera();
            localStorage.removeItem('cameraWasActive');
        }, 500);
    }
    
    // Registro para manejo de navegación entre páginas
    registerNavigationHandlers();
});

// Usar BroadcastChannel para comunicación entre pestañas
let cameraBroadcastChannel;
try {
    cameraBroadcastChannel = new BroadcastChannel('camera-channel');
    
    // Escuchar mensajes de otras pestañas
    cameraBroadcastChannel.onmessage = function(event) {
        if (event.data && event.data.type === 'CAMERA_STATUS') {
            console.log('Recibido estado de cámara desde otra pestaña:', event.data);
            
            // Si la cámara debería estar activa pero no lo está, iniciarla
            if (event.data.active && !cameraStream) {
                startCamera();
            }
        }
    };
    
    // Enviar estado actual a otras pestañas
    function broadcastCameraStatus() {
        try {
            cameraBroadcastChannel.postMessage({
                type: 'CAMERA_STATUS',
                active: cameraState.active,
                timestamp: Date.now()
            });
        } catch (e) {
            console.error('Error al transmitir estado de cámara:', e);
        }
    }
    
    // Transmitir estado periódicamente
    setInterval(broadcastCameraStatus, 5000);
} catch (e) {
    console.warn('BroadcastChannel no soportado en este navegador:', e);
}

// Crear elementos de la cámara en el DOM
function createCameraElements() {
    console.log('Creando elementos de cámara en el DOM...');
    
    // Verificar si ya existen para evitar duplicados
    if (document.getElementById('global-camera-container')) {
        console.log('Elementos de cámara ya existen, usando los existentes');
        cameraContainer = document.getElementById('global-camera-container');
        cameraWindow = document.getElementById('camera-window');
        cameraHeader = document.getElementById('camera-header');
        plateAlert = document.getElementById('plate-alert');
        return;
    }
    
    // Contenedor principal
    cameraContainer = document.createElement('div');
    cameraContainer.id = 'global-camera-container';
    cameraContainer.style.position = 'fixed';
    cameraContainer.style.zIndex = '9999'; // Muy alto para estar sobre todo
    document.body.appendChild(cameraContainer);
    
    // Ventana flotante de la cámara
    cameraWindow = document.createElement('div');
    cameraWindow.id = 'camera-window';
    cameraWindow.style.position = 'fixed';
    cameraWindow.style.zIndex = '10000';
    cameraWindow.style.backgroundColor = '#fff';
    cameraWindow.style.borderRadius = '5px';
    cameraWindow.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.3)';
    cameraWindow.style.width = window.cameraState.size.width || '256px';
    cameraWindow.style.height = window.cameraState.size.height || '224px';
    cameraWindow.style.maxHeight = '90vh'; // Máximo 90% de la altura de la ventana
    
    // Aplicar posición guardada o predeterminada
    if (window.cameraState.position.left) {
        cameraWindow.style.left = window.cameraState.position.left;
        cameraWindow.style.right = 'auto';
    } else {
        cameraWindow.style.right = window.cameraState.position.right || '20px';
    }
    
    if (window.cameraState.position.top) {
        cameraWindow.style.top = window.cameraState.position.top;
        cameraWindow.style.bottom = 'auto';
    } else {
        cameraWindow.style.bottom = window.cameraState.position.bottom || '20px';
    }
    
    cameraWindow.style.overflow = 'hidden';
    cameraWindow.style.resize = 'both'; // Permitir redimensionar
    cameraWindow.style.display = 'none';
    document.body.appendChild(cameraWindow);
    
    // Cabecera de la ventana
    cameraHeader = document.createElement('div');
    cameraHeader.id = 'camera-header';
    cameraHeader.style.backgroundColor = '#3498db';
    cameraHeader.style.color = 'white';
    cameraHeader.style.padding = '8px 12px';
    cameraHeader.style.cursor = 'move';
    cameraHeader.style.display = 'flex';
    cameraHeader.style.justifyContent = 'space-between';
    cameraHeader.style.alignItems = 'center';
    cameraHeader.style.userSelect = 'none'; // Evitar selección de texto al arrastrar
    cameraHeader.innerHTML = `
        <span>Cámara en vivo</span>
        <div>
            <button id="minimize-camera" class="btn btn-sm text-white"><i class="ti ti-minus"></i></button>
            <button id="close-camera" class="btn btn-sm text-white"><i class="ti ti-x"></i></button>
        </div>
    `;
    cameraWindow.appendChild(cameraHeader);
    
    // Contenido de la cámara
    const cameraContent = document.createElement('div');
    cameraContent.id = 'camera-content';
    cameraContent.style.padding = '10px';
    cameraContent.style.position = 'relative';
    cameraContent.style.height = 'calc(100% - 38px)'; // Altura restante después de header
    cameraContent.style.display = 'flex';
    cameraContent.style.flexDirection = 'column';
    cameraContent.innerHTML = `
        <video id="camera-feed" autoplay style="width:100%;flex-grow:1;display:block;"></video>
        <div class="camera-controls" style="display:flex;justify-content:space-between;padding:10px;background-color:#f1f1f1;margin-top: auto;">
            <span id="plate-status">Esperando placa...</span>
            <button id="toggle-recognition" class="btn btn-sm btn-outline-primary">Pausar</button>
        </div>
    `;
    cameraWindow.appendChild(cameraContent);
    
    // Alerta de placa - Asegurarnos que sea independiente de la ventana
    plateAlert = document.createElement('div');
    plateAlert.id = 'plate-alert';
    plateAlert.style.position = 'fixed';
    plateAlert.style.zIndex = '10001';
    plateAlert.style.backgroundColor = 'white';
    plateAlert.style.borderRadius = '5px';
    plateAlert.style.boxShadow = '0 0 15px rgba(0, 0, 0, 0.4)';
    plateAlert.style.padding = '15px';
    plateAlert.style.width = '350px';
    plateAlert.style.left = '50%';
    plateAlert.style.top = '50%';
    plateAlert.style.transform = 'translate(-50%, -50%)';
    plateAlert.style.display = 'none';
    
    // Contenido HTML del alerta de placa
    plateAlert.innerHTML = `
        <div class="text-center mb-3">
            <i class="ti ti-license text-success" style="font-size: 3rem;"></i>
            <h5 class="mt-2 mb-0">¡Placa Detectada!</h5>
            <div class="badge bg-primary mt-2 fs-5" id="detected-plate">ABC-123</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Tipo de Vehículo</label>
            <select id="vehicle-type" class="form-select">
                <option value="auto">Auto</option>
                <option value="moto">Moto</option>
                <option value="camioneta">Camioneta</option>
                <option value="otro">Otro</option>
            </select>
        </div>
        <div class="d-flex justify-content-between">
            <button id="cancel-plate-btn" class="btn btn-outline-secondary">Cancelar</button>
            <button id="confirm-plate-btn" class="btn btn-success">Registrar Entrada</button>
        </div>
    `;
    document.body.appendChild(plateAlert);
}

// Configurar listeners de eventos
function setupEventListeners() {
    console.log('Configurando eventos para la cámara...');
    
    // Botón de cerrar cámara
    const closeBtn = document.getElementById('close-camera');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeCamera);
    }
    
    // Botón de minimizar cámara
    const minimizeBtn = document.getElementById('minimize-camera');
    if (minimizeBtn) {
        minimizeBtn.addEventListener('click', minimizeCamera);
    }
    
    // Botón de toggle reconocimiento
    const toggleBtn = document.getElementById('toggle-recognition');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleRecognition);
    }
    
    // Botones de la alerta de placa
    setupCancelButton();
    setupConfirmButton();
    
    // Hacemos la ventana arrastrable
    makeDraggable(cameraWindow, cameraHeader);
    
    // Evento para guardar dimensiones al redimensionar
    cameraWindow.addEventListener('mouseup', function() {
        // Guardar tamaño actual después de redimensionar
        window.cameraState.size = {
            width: cameraWindow.style.width,
            height: cameraWindow.style.height
        };
        saveCameraState();
    });
    
    // Verificar si hay un checkbox de cámara persistente para sincronización
    const persistentCameraSwitch = document.getElementById('persistent-camera-switch');
    if (persistentCameraSwitch) {
        persistentCameraSwitch.addEventListener('change', function() {
            window.cameraState.active = this.checked;
            
            if (this.checked) {
                startCamera();
            } else {
                stopCamera();
            }
            
            saveCameraState();
        });
    }
    
    // Sincronizar con botón mostrar ventana si existe
    const showCameraBtn = document.getElementById('show-camera-window');
    if (showCameraBtn) {
        showCameraBtn.addEventListener('click', function() {
            if (!window.cameraState.active) {
                const persistentSwitch = document.getElementById('persistent-camera-switch');
                if (persistentSwitch) {
                    persistentSwitch.checked = true;
                }
                window.cameraState.active = true;
                startCamera();
            }
            
            if (cameraWindow.style.display === 'none') {
                cameraWindow.style.display = 'block';
            }
        });
    }
}

// Configurar eventos de visibilidad para mantener la cámara activa
function setupVisibilityEvents() {
    // Detectar cuando la página pierde el foco
    document.addEventListener('visibilitychange', handleVisibilityChange);
    
    // Detectar cuando la ventana pierde el foco
    window.addEventListener('blur', handleWindowBlur);
    
    // Detectar cuando la ventana recupera el foco
    window.addEventListener('focus', handleWindowFocus);
    
    // Ping periódico para mantener la cámara activa
    startKeepAlivePing();
    
    console.log('Eventos de visibilidad configurados');
}

// Manejar cambios de visibilidad del documento
function handleVisibilityChange() {
    if (document.visibilityState === 'visible') {
        console.log('Página visible nuevamente, verificando cámara...');
        checkAndRestoreCamera();
    } else {
        console.log('Página oculta, marcando estado para restauración...');
        // Guardar estado que indica que la cámara debe restaurarse
        if (cameraStream && cameraState.active) {
            localStorage.setItem('cameraWasActive', 'true');
        }
    }
}

// Manejar pérdida de foco de la ventana
function handleWindowBlur() {
    console.log('Ventana perdió el foco');
    // Guardar estado que indica que la cámara debe restaurarse
    if (cameraStream && cameraState.active) {
        localStorage.setItem('cameraWasActive', 'true');
    }
}

// Manejar recuperación de foco de la ventana
function handleWindowFocus() {
    console.log('Ventana recuperó el foco, verificando cámara...');
    checkAndRestoreCamera();
}

// Verificar y restaurar la cámara si es necesario
function checkAndRestoreCamera() {
    const wasActive = localStorage.getItem('cameraWasActive') === 'true';
    
    // Verificar si la cámara estaba activa pero ahora está detenida
    if (wasActive && (!cameraStream || cameraStream.getTracks().some(track => !track.enabled || track.readyState === 'ended'))) {
        console.log('Restaurando cámara que estaba activa...');
        
        // Si hay un stream existente pero no está activo, detenerlo primero
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
        
        // Reiniciar la cámara
        startCamera();
    }
    
    // Limpiar el estado temporal
    localStorage.removeItem('cameraWasActive');
}

// Ping periódico para mantener la cámara activa
function startKeepAlivePing() {
    // Cada 5 segundos, verificar si la cámara sigue activa
    setInterval(() => {
        if (cameraState.active) {
            // Verificar si la cámara sigue activa
            if (cameraStream) {
                const activeTracks = cameraStream.getTracks().filter(track => track.enabled && track.readyState === 'live');
                if (activeTracks.length === 0) {
                    console.log('Cámara inactiva detectada, intentando restaurar...');
                    // Reiniciar la cámara
                    cameraStream = null;
                    startCamera();
                }
            } else if (cameraState.active) {
                // La cámara debería estar activa pero no hay stream
                console.log('Stream ausente, intentando restaurar...');
                startCamera();
            }
        }
    }, 5000);
}

// Configura adecuadamente el evento del botón cancelar
function setupCancelButton() {
    const cancelBtn = document.getElementById('cancel-plate-btn');
    if (cancelBtn) {
        // Eliminar cualquier listener existente para evitar duplicados
        cancelBtn.removeEventListener('click', cancelRegistration);
        
        // Añadir el evento de forma más directa
        cancelBtn.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            cancelRegistration();
            return false;
        };
        
        console.log('Botón cancelar configurado correctamente');
    }
}

// Inicializar el sistema de cámara
function initCameraSystem() {
    // Verificar soporte para MediaDevices
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        console.log('El navegador no soporta acceso a cámara');
        simulationMode = true;
        return;
    }
    
    // Solicitar permisos con opciones más relajadas
    console.log('Solicitando permisos de cámara...');
    navigator.mediaDevices.getUserMedia({ 
        video: {
            // Sin restricciones específicas para maximizar compatibilidad
            width: { ideal: 640 },
            height: { ideal: 480 }
        } 
    })
    .then(stream => {
        console.log('Permisos de cámara concedidos');
        // Detener el stream inmediatamente, solo queríamos verificar permisos
        stream.getTracks().forEach(track => track.stop());
        simulationMode = false;
        
        // Aplicar estado guardado después de confirmar que hay cámara
        const savedState = localStorage.getItem('cameraState');
        if (savedState) {
            try {
                const state = JSON.parse(savedState);
                if (state.active) {
                    console.log('Auto-iniciando cámara basado en estado guardado');
                    setTimeout(() => startCamera(), 500);
                }
            } catch (e) {
                console.error('Error al procesar estado guardado:', e);
            }
        }
    })
    .catch(error => {
        console.error('Error al solicitar permisos:', error);
        // Mostrar mensaje de error más claro
        if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
            console.warn('Permiso de cámara denegado por el usuario');
            alert('No se ha podido acceder a la cámara porque los permisos fueron denegados. Por favor, permita el acceso a la cámara en la configuración de su navegador.');
        } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
            console.warn('No se encontró ninguna cámara en el dispositivo');
        } else {
            console.warn('Error al acceder a la cámara:', error.name);
        }
        simulationMode = true;
        
        // Incluso en modo simulación, verificar si debemos iniciar la cámara
        const savedState = localStorage.getItem('cameraState');
        if (savedState) {
            try {
                const state = JSON.parse(savedState);
                if (state.active) {
                    console.log('Auto-iniciando cámara en modo simulación');
                    setTimeout(() => startCamera(), 500);
                }
            } catch (e) {
                console.error('Error al procesar estado guardado:', e);
            }
        }
    });
    
    cameraState.initialized = true;
    window.cameraState.initialized = true;
}

// Iniciar la cámara
function startCamera() {
    console.log('Iniciando cámara...');
    
    if (cameraStream) {
        console.log('La cámara ya está activa, no es necesario reiniciar');
        return;
    }
    
    // Activar el estado global
    window.cameraState.active = true;
    
    // Mostrar la ventana
    if (cameraWindow) {
        cameraWindow.style.display = 'block';
    }
    
    // Activar el checkbox si existe
    const persistentCameraSwitch = document.getElementById('persistent-camera-switch');
    if (persistentCameraSwitch) {
        persistentCameraSwitch.checked = true;
    }
    
    // Iniciar en modo simulación por defecto
    startSimulationMode();
    
    // Guardar estado
    saveCameraState();
}

// Iniciar modo simulación (para desarrollo/pruebas)
function startSimulationMode() {
    console.log('Iniciando en modo simulación...');
    simulationMode = true;
    
    // Obtener el elemento de video
    const videoElement = document.getElementById('camera-feed');
    if (!videoElement) {
        console.error('No se encontró el elemento de video');
        return;
    }
    
    // Crear un canvas para simular video
    const canvas = document.createElement('canvas');
    canvas.width = 640;
    canvas.height = 480;
    const ctx = canvas.getContext('2d');
    
    // Colores y configuración para la simulación
    const colors = ['#3498db', '#2ecc71', '#e74c3c', '#f1c40f', '#9b59b6'];
    let currentColor = 0;
    
    // Función para dibujar un fotograma
    function drawFrame() {
        // Cambiar color cada 50 fotogramas
        if (Math.random() < 0.02) {
            currentColor = (currentColor + 1) % colors.length;
        }
        
        // Limpiar canvas
        ctx.fillStyle = '#f8f9fa';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Dibujar un rectángulo central que cambia de color
        ctx.fillStyle = colors[currentColor];
        ctx.fillRect(canvas.width / 4, canvas.height / 4, canvas.width / 2, canvas.height / 2);
        
        // Dibujar texto
        ctx.fillStyle = '#000';
        ctx.font = '24px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('MODO SIMULACIÓN', canvas.width / 2, canvas.height / 2);
        ctx.font = '18px Arial';
        ctx.fillText('Placa ABC-123', canvas.width / 2, canvas.height / 2 + 30);
        
        // Dibujar fecha y hora
        const now = new Date();
        ctx.font = '14px Arial';
        ctx.fillText(now.toLocaleString(), canvas.width / 2, canvas.height - 20);
        
        // Convertir a stream
        try {
            const frame = canvas.captureStream(30).getVideoTracks()[0];
            
            // Si no hay stream actual, crear uno nuevo
            if (!cameraStream) {
                const stream = new MediaStream([frame]);
                cameraStream = stream;
                
                // Asignar al elemento de video
                videoElement.srcObject = stream;
                
                // Exponer globalmente
                window.cameraStream = stream;
                
                console.log('Stream de simulación iniciado');
            }
        } catch (e) {
            console.error('Error creando stream de simulación:', e);
        }
        
        // Continuar dibujando si seguimos en modo simulación
        if (simulationMode && window.cameraState.active) {
            requestAnimationFrame(drawFrame);
        }
    }
    
    // Iniciar la animación
    drawFrame();
    
    // Iniciar reconocimiento de placas simulado
    startPlateRecognition();
}

// Cerrar/ocultar la cámara
function closeCamera() {
    console.log('Cerrando cámara...');
    
    // Ocultar ventana
    if (cameraWindow) {
        cameraWindow.style.display = 'none';
    }
    
    // No detener el stream para que siga funcionando en segundo plano
    // Solo detener si se desactiva completamente
}

// Minimizar la ventana de cámara
function minimizeCamera() {
    if (!cameraWindow) return;
    
    if (!minimized) {
        // Guardar altura actual para restaurarla después
        minimizedPrevHeight = cameraWindow.style.height;
        
        // Reducir a solo la cabecera
        cameraWindow.style.height = '38px';
        
        // Ocultar contenido
        const cameraContent = document.getElementById('camera-content');
        if (cameraContent) {
            cameraContent.style.display = 'none';
        }
        
        minimized = true;
    } else {
        // Restaurar altura previa
        cameraWindow.style.height = minimizedPrevHeight || '224px';
        
        // Mostrar contenido
        const cameraContent = document.getElementById('camera-content');
        if (cameraContent) {
            cameraContent.style.display = 'flex';
        }
        
        minimized = false;
    }
    
    // Guardar estado
    saveCameraState();
}

// Iniciar reconocimiento simulado de placas
function startPlateRecognition() {
    console.log('Iniciando reconocimiento de placas (simulado)');
    
    recognitionActive = true;
    
    if (plateRecognitionTimer) {
        clearInterval(plateRecognitionTimer);
    }
    
    // Simular detección cada 10-20 segundos
    plateRecognitionTimer = setInterval(simulatePlateDetection, 15000);
    
    // Actualizar estado del botón
    const toggleBtn = document.getElementById('toggle-recognition');
    if (toggleBtn) {
        toggleBtn.textContent = 'Pausar';
        toggleBtn.className = 'btn btn-sm btn-outline-primary';
    }
}

// Detener reconocimiento de placas
function stopPlateRecognition() {
    console.log('Deteniendo reconocimiento de placas');
    
    recognitionActive = false;
    
    if (plateRecognitionTimer) {
        clearInterval(plateRecognitionTimer);
        plateRecognitionTimer = null;
    }
    
    // Actualizar estado del botón
    const toggleBtn = document.getElementById('toggle-recognition');
    if (toggleBtn) {
        toggleBtn.textContent = 'Reanudar';
        toggleBtn.className = 'btn btn-sm btn-outline-secondary';
    }
}

// Alternar reconocimiento de placas
function toggleRecognition() {
    if (recognitionActive) {
        stopPlateRecognition();
    } else {
        startPlateRecognition();
    }
}

// Simular detección de placa
function simulatePlateDetection() {
    if (!recognitionActive || !window.cameraState.active) return;
    
    // Generar placa aleatoria
    const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const digits = '0123456789';
    
    let plate = '';
    for (let i = 0; i < 3; i++) {
        plate += letters.charAt(Math.floor(Math.random() * letters.length));
    }
    plate += '-';
    for (let i = 0; i < 3; i++) {
        plate += digits.charAt(Math.floor(Math.random() * digits.length));
    }
    
    // Mostrar la alerta con la placa
    const plateElement = document.getElementById('detected-plate');
    if (plateElement) {
        plateElement.textContent = plate;
    }
    
    // Mostrar la alerta
    if (plateAlert) {
        plateAlert.style.display = 'block';
    }
    
    // Actualizar estado
    document.getElementById('plate-status').textContent = `Última: ${plate}`;
    lastDetectedPlate = plate;
    
    console.log('Placa detectada (simulación):', plate);
}

// Detener completamente la cámara
function stopCamera() {
    console.log('Deteniendo cámara completamente...');
    
    // Detener reconocimiento
    stopPlateRecognition();
    
    // Ocultar ventana
    if (cameraWindow) {
        cameraWindow.style.display = 'none';
    }
    
    // Detener stream de video
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
        window.cameraStream = null;
    }
    
    // Actualizar estado
    window.cameraState.active = false;
    
    // Desactivar checkbox si existe
    const persistentCameraSwitch = document.getElementById('persistent-camera-switch');
    if (persistentCameraSwitch) {
        persistentCameraSwitch.checked = false;
    }
    
    // Guardar estado
    saveCameraState();
}

// Registro de eventos para navegación entre páginas
function registerNavigationHandlers() {
    console.log('Registrando manejadores de navegación...');
    
    // Guardar estado antes de navegación
    window.addEventListener('beforeunload', function() {
        if (window.cameraState.active) {
            localStorage.setItem('cameraWasActive', 'true');
            
            // Guardar posición y dimensiones actuales
            saveCameraState();
        }
    });
    
    // Interceptar clicks en links para guardar estado
    document.addEventListener('click', function(e) {
        // Buscar si se ha hecho clic en un enlace
        let target = e.target;
        while (target && target.tagName !== 'A') {
            target = target.parentNode;
            if (!target || target === document) break;
        }
        
        // Si es un enlace válido del mismo origen
        if (target && target.tagName === 'A' && target.href && 
            target.href.startsWith(window.location.origin) && 
            !target.getAttribute('download')) {
            
            // Guardar estado de cámara si está activa
            if (window.cameraState.active) {
                localStorage.setItem('cameraWasActive', 'true');
                saveCameraState();
            }
        }
    });
}

// Exponer funciones globalmente
window.startGlobalCamera = startCamera;
window.stopCamera = stopCamera;

// Función para hacer la ventana arrastrable
function makeDraggable(element, handle) {
    if (!element || !handle) return;
    
    handle.onmousedown = dragMouseDown;
    
    function dragMouseDown(e) {
        e.preventDefault();
        
        // Obtener la posición inicial del mouse
        let startX = e.clientX;
        let startY = e.clientY;
        
        // Obtener la posición inicial del elemento
        let startLeft = element.offsetLeft;
        let startTop = element.offsetTop;
        
        // Activar el movimiento
        isDragging = true;
        dragStartPos = { 
            mouseX: startX, 
            mouseY: startY,
            elemLeft: startLeft,
            elemTop: startTop
        };
        
        // Añadir eventos para el movimiento y para cuando se suelta
        document.onmouseup = closeDragElement;
        document.onmousemove = elementDrag;
    }
    
    function elementDrag(e) {
        e.preventDefault();
        
        if (!isDragging) return;
        
        // Calcular la nueva posición
        let dx = e.clientX - dragStartPos.mouseX;
        let dy = e.clientY - dragStartPos.mouseY;
        
        // Establecer la nueva posición
        let newLeft = dragStartPos.elemLeft + dx;
        let newTop = dragStartPos.elemTop + dy;
        
        // Restricciones para no salir de la ventana
        newLeft = Math.max(0, Math.min(window.innerWidth - element.offsetWidth, newLeft));
        newTop = Math.max(0, Math.min(window.innerHeight - element.offsetHeight, newTop));
        
        element.style.left = newLeft + 'px';
        element.style.top = newTop + 'px';
        element.style.right = 'auto';
        element.style.bottom = 'auto';
        
        // Actualizar posición en el estado
        window.cameraState.position = {
            left: newLeft + 'px',
            top: newTop + 'px',
            right: 'auto',
            bottom: 'auto'
        };
    }
    
    function closeDragElement() {
        // Detener el movimiento
        isDragging = false;
        document.onmouseup = null;
        document.onmousemove = null;
        
        // Guardar posición final
        saveCameraState();
    }
}

// Guardar el estado actual de la cámara
function saveCameraState() {
    try {
        // Capturar el estado actual
        const state = {
            active: window.cameraState.active,
            position: {
                left: cameraWindow.style.left,
                top: cameraWindow.style.top,
                right: cameraWindow.style.right,
                bottom: cameraWindow.style.bottom
            },
            size: {
                width: cameraWindow.style.width,
                height: cameraWindow.style.height
            },
            minimized: minimized
        };
        
        // Guardar en localStorage
        localStorage.setItem('cameraState', JSON.stringify(state));
        console.log('Estado guardado:', state);
        
        // Actualizar estado global también
        window.cameraState = state;
    } catch (e) {
        console.error('Error guardando estado de cámara:', e);
    }
}

// Restaurar el estado de la cámara desde un objeto de estado
function restoreCameraState(state) {
    if (!state || !cameraWindow) return;
    
    try {
        // Restaurar tamaño
        if (state.size) {
            cameraWindow.style.width = state.size.width || '256px';
            cameraWindow.style.height = state.size.height || '224px';
        }
        
        // Restaurar posición
        if (state.position) {
            if (state.position.left && state.position.left !== 'auto') {
                cameraWindow.style.left = state.position.left;
                cameraWindow.style.right = 'auto';
            } else if (state.position.right && state.position.right !== 'auto') {
                cameraWindow.style.right = state.position.right;
                cameraWindow.style.left = 'auto';
            }
            
            if (state.position.top && state.position.top !== 'auto') {
                cameraWindow.style.top = state.position.top;
                cameraWindow.style.bottom = 'auto';
            } else if (state.position.bottom && state.position.bottom !== 'auto') {
                cameraWindow.style.bottom = state.position.bottom;
                cameraWindow.style.top = 'auto';
            }
        }
        
        // Restaurar estado de minimizado
        if (state.minimized) {
            minimizedPrevHeight = cameraWindow.style.height;
            cameraWindow.style.height = '38px'; // Solo mostrar la cabecera
            const cameraContent = document.getElementById('camera-content');
            if (cameraContent) {
                cameraContent.style.display = 'none';
            }
            minimized = true;
        }
        
        // Restaurar visibilidad
        if (state.active) {
            cameraWindow.style.display = 'block';
        }
        
        console.log('Estado restaurado correctamente');
    } catch (e) {
        console.error('Error restaurando estado de cámara:', e);
    }
}

// Configurar el botón de confirmar
function setupConfirmButton() {
    const confirmBtn = document.getElementById('confirm-plate-btn');
    if (confirmBtn) {
        // Eliminar cualquier listener existente para evitar duplicados
        confirmBtn.removeEventListener('click', confirmRegistration);
        
        // Añadir el evento de forma más directa
        confirmBtn.onclick = confirmRegistration;
        
        console.log('Botón confirmar configurado correctamente');
    }
}

// Confirmar registro
function confirmRegistration() {
    const plate = lastDetectedPlate;
    const vehicleType = document.getElementById('vehicle-type').value;
    
    console.log('Registrando vehículo:', {
        placa: plate,
        tipo: vehicleType
    });
    
    // Ocultar alerta
    plateAlert.style.display = 'none';
    
    // Mostrar indicador de carga
    Swal.fire({
        title: 'Registrando vehículo',
        html: `Registrando vehículo con placa: <strong>${plate}</strong>`,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
            
            // Enviar datos al servidor
            const formData = new FormData();
            formData.append('placa', plate);
            formData.append('tipo_vehiculo', vehicleType);
            
            fetch('controladores/registro_automatico.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Vehículo registrado',
                        text: data.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'Error desconocido al registrar el vehículo'
                    });
                }
            })
            .catch(error => {
                console.error('Error al registrar:', error);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión al intentar registrar el vehículo. Por favor, capture la placa manualmente.'
                });
            });
        }
    });
}

// Cancelar registro
function cancelRegistration() {
    console.log('Cancelando registro...');
    
    try {
        // Verificar si el elemento existe
        if (plateAlert) {
            plateAlert.style.display = 'none';
        } else {
            // Si plateAlert es null, intentar obtenerlo del DOM
            const plateAlertElement = document.getElementById('plate-alert');
            if (plateAlertElement) {
                plateAlertElement.style.display = 'none';
            }
        }
        
        // Asegurarse de que cualquier SweetAlert abierto también se cierre
        if (typeof Swal !== 'undefined') {
            if (Swal.isVisible()) {
                Swal.close();
            }
        }
        
        // Seleccionar TODOS los posibles elementos del modal y ocultarlos
        const modalElements = document.querySelectorAll('#plate-alert, .swal2-container, [id^="plate-alert"]');
        modalElements.forEach(function(modal) {
            if (modal) {
                try {
                    modal.style.display = 'none';
                } catch (e) {
                    console.error('Error al ocultar modal:', e);
                }
            }
        });
        
        // Como última opción, insertar CSS que oculte el modal forzosamente
        const style = document.createElement('style');
        style.innerHTML = `
            #plate-alert, .swal2-container { 
                display: none !important; 
                visibility: hidden !important; 
                opacity: 0 !important;
                z-index: -9999 !important;
            }
        `;
        document.head.appendChild(style);
        
        // Eliminar el estilo después de 100ms
        setTimeout(() => {
            try {
                document.head.removeChild(style);
            } catch (e) {}
        }, 100);
        
    } catch (e) {
        console.error('Error en cancelRegistration:', e);
    }
    
    return false;
} 