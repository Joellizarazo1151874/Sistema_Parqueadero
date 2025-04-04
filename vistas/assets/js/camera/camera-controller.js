/**
 * Camera Controller for SmartPark
 * Manages camera access, video streaming, and license plate recognition
 */

class CameraController {
    constructor() {
        this.videoElement = null;
        this.stream = null;
        this.isActive = false;
        this.isFloatingWindowOpen = false;
        this.floatingWindow = null;
        this.scanInterval = 3000; // Default scan interval in milliseconds
        this.scanIntervalId = null;
        this.lastDetectedPlate = '';
        this.enableNotifications = true;
        this.sensitivity = 5;
        this.resolution = { width: 640, height: 480 };
        this.autoStart = true;
        this.cameraId = '';
        this.windowPosition = { top: 'auto', left: 'auto' }; // Para recordar la posición de la ventana
        
        // Bind methods
        this.startCamera = this.startCamera.bind(this);
        this.stopCamera = this.stopCamera.bind(this);
        this.scanForPlate = this.scanForPlate.bind(this);
        this.openFloatingWindow = this.openFloatingWindow.bind(this);
        this.closeFloatingWindow = this.closeFloatingWindow.bind(this);
        this.loadSettings = this.loadSettings.bind(this);
        this.saveSettings = this.saveSettings.bind(this);
        
        // Verificar si la ventana flotante estaba abierta antes
        this.checkFloatingWindowState();
    }

    /**
     * Initialize the camera controller
     */
    /**
     * Verifica si la ventana flotante estaba abierta antes
     * y la reabre si es necesario, manteniendo el estado de la cámara
     */
    checkFloatingWindowState() {
        const wasOpen = localStorage.getItem('cameraWindowOpen') === 'true';
        const wasActive = localStorage.getItem('cameraActive') === 'true';
        const savedTop = localStorage.getItem('cameraWindowTop');
        const savedLeft = localStorage.getItem('cameraWindowLeft');

        console.log('checkFloatingWindowState: Verificando estado guardado ->', { wasOpen, wasActive, savedTop, savedLeft });

        if (wasOpen && !this.isFloatingWindowOpen) {
            console.log('checkFloatingWindowState: Ventana estaba abierta, intentando restaurar...');

            // Asegurarnos de que la posición se restaure antes de mostrar la ventana
            if (savedTop && savedLeft) {
                this.windowPosition = { top: savedTop, left: savedLeft };
                console.log('checkFloatingWindowState: Posición restaurada ->', this.windowPosition);
            }

            // Crear la ventana flotante primero
            this.createFloatingWindow();

            // Si la cámara estaba activa antes, intentar activarla nuevamente después de que la ventana esté lista
            if (wasActive) {
                console.log('checkFloatingWindowState: Cámara estaba activa, intentando reiniciar stream...');
                // Esperar un poco más para asegurar que el DOM de la ventana esté listo
                setTimeout(() => {
                    this.startCamera().catch(error => {
                        console.error('Error al reiniciar la cámara automáticamente:', error);
                        // Si falla el reinicio (p.ej. permisos), actualizar estado
                        localStorage.setItem('cameraActive', 'false');
                        this.isActive = false;
                        // Actualizar UI si es necesario
                        const statusBar = this.floatingWindow?.querySelector('#cameraStatusBar span');
                        if (statusBar) statusBar.textContent = 'Error al reiniciar cámara. Haga clic para intentar.';
                    });
                }, 700); // Aumentamos ligeramente el tiempo de espera
            }
        } else {
            console.log('checkFloatingWindowState: Ventana no estaba abierta o ya está visible.');
        }
    }
    
    /**
     * Initialize the camera controller
     */
    async init() {
        this.loadSettings();
        
        // Determinar si estamos en la página de configuración
        const isConfigPage = document.getElementById('tab8') !== null;
        
        console.log('Inicializando CameraController en página de configuración:', isConfigPage);
        
        // Configurar el botón de cámara en el encabezado en todas las páginas
        this.setupCameraButton();
        
        if (isConfigPage) {
            // En la página de configuración, cargar los dispositivos de cámara solo si es necesario
            await this.populateCameraDevices();
            
            // Solo iniciar automáticamente en la página de configuración si autoStart está activado
            if (this.autoStart && !this.isFloatingWindowOpen) {
                setTimeout(() => {
                    this.openFloatingWindow(false); // No iniciar la cámara automáticamente
                }, 1000);
            }
        } else {
            // En otras páginas, solo verificar si la ventana flotante estaba abierta
            this.checkFloatingWindowState();
        }
    }
    
    /**
     * Configurar el botón de cámara en el encabezado
     */
    setupCameraButton() {
        const openCameraBtn = document.getElementById('openCameraBtn');
        if (openCameraBtn) {
            // Eliminar listeners previos para evitar duplicados
            openCameraBtn.replaceWith(openCameraBtn.cloneNode(true));
            
            // Obtener la referencia al nuevo botón
            const newOpenCameraBtn = document.getElementById('openCameraBtn');
            
            // Añadir el nuevo listener
            newOpenCameraBtn.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('Botón de cámara clickeado');
                
                if (this.isFloatingWindowOpen) {
                    this.closeFloatingWindow();
                } else {
                    // Solo crear la ventana flotante sin iniciar la cámara
                    this.createFloatingWindow();
                }
            });
            
            console.log('Botón de cámara configurado correctamente');
        } else {
            console.warn('Botón de cámara no encontrado en el DOM');
        }
    }
    
    /**
     * Populate the camera devices dropdown
     */
    async populateCameraDevices() {
        try {
            // Solo solicitamos permiso si estamos en la página de configuración
            const isConfigPage = document.getElementById('tab8') !== null;
            
            if (isConfigPage) {
                // Intentar obtener la lista de dispositivos sin solicitar permiso primero
                let devices = await navigator.mediaDevices.enumerateDevices();
                let videoDevices = devices.filter(device => device.kind === 'videoinput');
                
                // Si no hay etiquetas de dispositivos, entonces solicitamos permiso
                const needsPermission = videoDevices.length > 0 && !videoDevices[0].label;
                
                if (needsPermission) {
                    // Solicitamos permiso solo si es necesario y estamos en la página de configuración
                    const permissionGranted = await this.requestCameraPermission(false);
                    if (permissionGranted) {
                        devices = await navigator.mediaDevices.enumerateDevices();
                        videoDevices = devices.filter(device => device.kind === 'videoinput');
                    }
                }
                
                const cameraSelect = document.getElementById('cameraSelect');
                if (cameraSelect) {
                    // Clear existing options except the first one
                    while (cameraSelect.options.length > 1) {
                        cameraSelect.remove(1);
                    }
                    
                    if (videoDevices.length === 0) {
                        // No se encontraron cámaras
                        const option = document.createElement('option');
                        option.value = "";
                        option.text = "No se encontraron cámaras";
                        cameraSelect.appendChild(option);
                        
                        console.log('No se detectaron cámaras');
                    } else {
                        // Add camera options
                        videoDevices.forEach((device, index) => {
                            const option = document.createElement('option');
                            option.value = device.deviceId;
                            option.text = device.label || `Cámara ${index + 1}`;
                            cameraSelect.appendChild(option);
                        });
                        
                        // Select the saved camera if it exists
                        if (this.cameraId && videoDevices.some(d => d.deviceId === this.cameraId)) {
                            cameraSelect.value = this.cameraId;
                        } else if (videoDevices.length > 0) {
                            // Select the first camera by default
                            this.cameraId = videoDevices[0].deviceId;
                            cameraSelect.value = this.cameraId;
                        }
                    }
                }
            }
        } catch (error) {
            console.error('Error enumerando dispositivos:', error);
            // No mostrar alerta aquí, solo registrar el error
        }
    }

    /**
     * Parse resolution string to width and height
     * @param {string} resolutionStr - Resolution string in format "widthxheight"
     * @returns {Object} - Object with width and height properties
     */
    parseResolution(resolutionStr) {
        const [width, height] = resolutionStr.split('x').map(Number);
        return { width, height };
    }

    /**
     * Request camera permission to get device labels
     * @param {boolean} showDialog - Si se debe mostrar un diálogo de confirmación
     * @returns {Promise<boolean>} - Si se concedieron los permisos
     */
    async requestCameraPermission(showDialog = true) {
        try {
            // Si showDialog es true, mostrar mensaje de solicitud de permisos
            if (showDialog) {
                console.log('Solicitando permisos de cámara...');
                const result = await Swal.fire({
                    title: 'Permisos de cámara',
                    text: 'Para usar el reconocimiento de placas, necesitamos acceder a tu cámara. Por favor, acepta los permisos cuando el navegador los solicite.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Continuar',
                    cancelButtonText: 'Cancelar'
                });
                
                if (!result.isConfirmed) {
                    return false;
                }
            }
            
            try {
                // Solicitar acceso a cualquier cámara
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    } 
                });
                
                // Detener inmediatamente el stream, solo necesitamos el permiso
                stream.getTracks().forEach(track => track.stop());
                
                if (showDialog) {
                    // Actualizar la lista de cámaras disponibles
                    const devices = await navigator.mediaDevices.enumerateDevices();
                    const videoDevices = devices.filter(device => device.kind === 'videoinput');
                    
                    if (videoDevices.length === 0) {
                        Swal.fire({
                            title: 'No se detectaron cámaras',
                            text: 'No se encontraron dispositivos de cámara conectados a este equipo.',
                            icon: 'warning'
                        });
                    } else {
                        Swal.fire({
                            title: 'Permisos concedidos',
                            text: 'Ahora puedes usar el reconocimiento de placas',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }
                
                return true;
            } catch (error) {
                console.error('Error al acceder a la cámara:', error);
                
                if (showDialog) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo acceder a la cámara. Verifica que has concedido los permisos necesarios.',
                        icon: 'error'
                    });
                }
                
                return false;
            }
        } catch (error) {
            console.error('Error al solicitar permiso de cámara:', error);
            return false;
        }
    }

    /**
     * Start the camera with the selected device and resolution
     */
    async startCamera() {
        // Si ya hay un stream activo, no hacer nada
        if (this.isActive && this.stream) {
            console.log('La cámara ya está activa');
            return;
        }
        
        try {
            // Detener cualquier stream anterior por si acaso
            this.stopCamera(false); // false = no cerrar la ventana
            
            // Obtener la configuración de la cámara
            let deviceId = this.cameraId;
            let resolution = this.resolution;
            
            // Si estamos en la página de configuración, usar los valores de los selectores
            const cameraSelect = document.getElementById('cameraSelect');
            const resolutionSelect = document.getElementById('resolutionSelect');
            
            if (cameraSelect && cameraSelect.options.length > 1) {
                deviceId = cameraSelect.value;
                this.cameraId = deviceId;
            }
            
            if (resolutionSelect) {
                resolution = this.parseResolution(resolutionSelect.value);
                this.resolution = resolution;
            }
            
            // Si no hay un ID de cámara seleccionado, intentar obtener uno
            if (!deviceId) {
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoDevices = devices.filter(device => device.kind === 'videoinput');
                
                if (videoDevices.length > 0) {
                    deviceId = videoDevices[0].deviceId;
                    this.cameraId = deviceId;
                } else {
                    throw new Error('No se encontraron cámaras disponibles');
                }
            }
            
            // Configurar las restricciones para getUserMedia
            const constraints = {
                video: {
                    deviceId: deviceId ? { exact: deviceId } : undefined,
                    width: { ideal: resolution.width },
                    height: { ideal: resolution.height }
                }
            };
            
            console.log('Intentando acceder a la cámara con:', constraints);
            
            // Solicitar acceso a la cámara
            this.stream = await navigator.mediaDevices.getUserMedia(constraints);
            
            // Si tenemos un elemento de video, asignarle el stream
            if (this.videoElement) {
                this.videoElement.srcObject = this.stream;
                this.videoElement.onloadedmetadata = () => {
                    this.videoElement.play().catch(e => console.error('Error reproduciendo video:', e));
                };
                this.isActive = true;
                
                // Guardar el estado activo en localStorage
                localStorage.setItem('cameraActive', 'true');
                
                // Iniciar el escaneo de placas
                this.startScanningPlates();
                
                // Actualizar la UI en la página de configuración
                const startBtn = document.getElementById('startCameraBtn');
                const stopBtn = document.getElementById('stopCameraBtn');
                if (startBtn) startBtn.disabled = true;
                if (stopBtn) stopBtn.disabled = false;
                
                // Actualizar la vista previa en la página de configuración
                const preview = document.getElementById('cameraPreview');
                if (preview) {
                    preview.innerHTML = '';
                    const previewVideo = document.createElement('video');
                    previewVideo.srcObject = this.stream;
                    previewVideo.autoplay = true;
                    previewVideo.style.maxWidth = '100%';
                    previewVideo.style.maxHeight = '100%';
                    preview.appendChild(previewVideo);
                }
                
                // Actualizar el estado en la ventana flotante
                const plateStatus = document.getElementById('plateStatus');
                if (plateStatus) {
                    plateStatus.textContent = 'Cámara activa. Esperando detección de placa...';
                }
            }
        } catch (error) {
            console.error('Error al iniciar la cámara:', error);
            
            // Actualizar el estado en la ventana flotante
            if (this.floatingWindow) {
                const plateStatus = document.getElementById('plateStatus');
                if (plateStatus) {
                    plateStatus.textContent = 'Error de cámara. Haga clic para reintentar.';
                }
            } else {
                // Solo mostrar alerta si no hay ventana flotante
                Swal.fire({
                    icon: 'error',
                    title: 'Error de cámara',
                    text: `No se pudo acceder a la cámara: ${error.message}. Verifique los permisos del navegador y que la cámara esté conectada correctamente.`,
                });
            }
        }
    }

    /**
     * Stop the camera stream
     * @param {boolean} closeWindow - Si se debe cerrar la ventana flotante
     */
    stopCamera(closeWindow = true) {
        // Detener el escaneo de placas
        this.stopScanningPlates();
        
        // Detener todos los tracks del stream
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
        
        // Limpiar el elemento de video
        if (this.videoElement) {
            this.videoElement.srcObject = null;
        }
        
        this.isActive = false;
        
        // Guardar el estado inactivo en localStorage
        localStorage.setItem('cameraActive', 'false');
        
        // Actualizar la UI en la página de configuración
        const startBtn = document.getElementById('startCameraBtn');
        const stopBtn = document.getElementById('stopCameraBtn');
        if (startBtn) startBtn.disabled = false;
        if (stopBtn) stopBtn.disabled = true;
        
        // Actualizar la vista previa en la página de configuración
        const preview = document.getElementById('cameraPreview');
        if (preview) {
            preview.innerHTML = `
                <i class="bi bi-camera-video-off" style="font-size: 48px;"></i>
                <p>La cámara no está activa</p>
            `;
        }
        
        // Actualizar el estado en la ventana flotante si no se va a cerrar
        if (!closeWindow && this.floatingWindow) {
            const plateStatus = document.getElementById('plateStatus');
            if (plateStatus) {
                plateStatus.textContent = 'Haga clic para activar la cámara';
            }
        }
    }

    /**
     * Start scanning for license plates at the configured interval
     */
    startScanningPlates() {
        // Clear any existing interval
        this.stopScanningPlates();
        
        // Get the scan interval from the input
        const scanIntervalInput = document.getElementById('scanInterval');
        if (scanIntervalInput) {
            this.scanInterval = parseInt(scanIntervalInput.value) * 1000;
        }
        
        // Start a new interval
        this.scanIntervalId = setInterval(this.scanForPlate, this.scanInterval);
    }

    /**
     * Stop scanning for license plates
     */
    stopScanningPlates() {
        if (this.scanIntervalId) {
            clearInterval(this.scanIntervalId);
            this.scanIntervalId = null;
        }
    }

    /**
     * Scan the current video frame for a license plate
     */
    async scanForPlate() {
        if (!this.isActive || !this.videoElement) return;
        
        try {
            // Create a canvas to capture the current video frame
            const canvas = document.createElement('canvas');
            canvas.width = this.videoElement.videoWidth;
            canvas.height = this.videoElement.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(this.videoElement, 0, 0, canvas.width, canvas.height);
            
            // Get the image data for processing
            const imageData = canvas.toDataURL('image/jpeg');
            
            // In a real implementation, you would send this image to a license plate recognition API
            // For this demo, we'll simulate recognition with a placeholder function
            const plate = await this.simulatePlateRecognition(imageData);
            
            if (plate && plate !== this.lastDetectedPlate) {
                this.lastDetectedPlate = plate;
                
                // Update UI with the detected plate
                const lastDetectedPlateInput = document.getElementById('lastDetectedPlate');
                if (lastDetectedPlateInput) {
                    lastDetectedPlateInput.value = plate;
                }
                
                // Play notification sound if enabled
                if (this.enableNotifications) {
                    this.playNotificationSound();
                }
                
                // Show the vehicle type selection modal
                this.showVehicleTypeModal(plate);
            }
        } catch (error) {
            console.error('Error scanning for plate:', error);
        }
    }

    /**
     * Simulate license plate recognition (placeholder for actual OCR)
     * In a real implementation, this would call an OCR API
     * @param {string} imageData - Base64 encoded image data
     * @returns {Promise<string|null>} - Recognized license plate or null
     */
    async simulatePlateRecognition(imageData) {
        // This is a placeholder function that would normally call an OCR API
        // For demo purposes, we'll randomly decide whether to "detect" a plate
        
        // Simulate processing time
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Randomly decide if we detect a plate (based on sensitivity)
        const detectionThreshold = (11 - this.sensitivity) * 10; // Higher sensitivity = lower threshold
        const randomValue = Math.random() * 100;
        
        if (randomValue > detectionThreshold) {
            // For demo, generate a random plate
            // In real implementation, this would be the result from OCR
            const testPlateBtn = document.getElementById('testRecognitionBtn');
            if (testPlateBtn && testPlateBtn.dataset.testPlate) {
                return testPlateBtn.dataset.testPlate;
            }
            
            // Generate a random plate if no test plate is set
            const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const numbers = '0123456789';
            
            let plate = '';
            // Format: 3 letters + 3 numbers
            for (let i = 0; i < 3; i++) {
                plate += letters.charAt(Math.floor(Math.random() * letters.length));
            }
            plate += '-';
            for (let i = 0; i < 3; i++) {
                plate += numbers.charAt(Math.floor(Math.random() * numbers.length));
            }
            
            return plate;
        }
        
        return null;
    }

    /**
     * Play a notification sound when a license plate is detected
     */
    playNotificationSound() {
        try {
            // Crear un sonido usando la API de AudioContext (funciona sin archivo de sonido)
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            // Configurar el sonido (frecuencia y volumen)
            oscillator.type = 'sine';
            oscillator.frequency.value = 880; // Nota A5
            gainNode.gain.value = 0.3; // Volumen bajo
            
            // Configurar la duración
            oscillator.start();
            
            // Reducir gradualmente el volumen
            gainNode.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + 0.5);
            
            // Detener después de 0.5 segundos
            setTimeout(() => {
                oscillator.stop();
            }, 500);
            
            console.log('Sonido de notificación reproducido');
        } catch (err) {
            console.log('Error reproduciendo sonido de notificación:', err);
        }
    }

    /**
     * Show a modal to select the vehicle type for the detected plate
     * @param {string} plate - The detected license plate
     */
    showVehicleTypeModal(plate) {
        // Fetch vehicle types from the database
        fetch('../../controladores/obtener_tipos_vehiculos.php')
            .then(response => response.json())
            .then(vehicleTypes => {
                // Create options for the select element
                const options = vehicleTypes.map(type => 
                    `<option value="${type}">${type}</option>`
                ).join('');
                
                // Show the modal with SweetAlert2
                Swal.fire({
                    title: 'Placa Detectada',
                    html: `
                        <p class="mb-4">Se ha detectado la placa: <strong>${plate}</strong></p>
                        <div class="form-group">
                            <label for="vehicleType">Tipo de Vehículo:</label>
                            <select id="vehicleType" class="form-control">
                                ${options}
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <label for="vehicleDescription">Descripción (opcional):</label>
                            <textarea id="vehicleDescription" class="form-control" rows="2"></textarea>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Registrar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    focusConfirm: false,
                    allowOutsideClick: false,
                    preConfirm: () => {
                        const vehicleType = document.getElementById('vehicleType').value;
                        const description = document.getElementById('vehicleDescription').value;
                        return { vehicleType, description };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.registerVehicle(plate, result.value.vehicleType, result.value.description);
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching vehicle types:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudieron cargar los tipos de vehículos.'
                });
            });
    }

    /**
     * Register the vehicle with the detected plate
     * @param {string} plate - The detected license plate
     * @param {string} vehicleType - The selected vehicle type
     * @param {string} description - Optional description
     */
    registerVehicle(plate, vehicleType, description) {
        // Create form data
        const formData = new FormData();
        formData.append('matricula', plate);
        formData.append('tipo_vehiculo', vehicleType);
        formData.append('descripcion', description);
        formData.append('tipo_registro', 'hora'); // Default to hourly rate
        
        // Send the data to the server
        fetch('../../controladores/registro_parqueo.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Vehículo Registrado',
                    text: `El vehículo con placa ${plate} ha sido registrado exitosamente.`,
                    timer: 3000,
                    timerProgressBar: true
                });
            } else {
                throw new Error('Error registering vehicle');
            }
        })
        .catch(error => {
            console.error('Error registering vehicle:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo registrar el vehículo. Inténtelo manualmente.'
            });
        });
    }

    /**
     * Crea la ventana flotante sin iniciar la cámara
     */
    createFloatingWindow() {
        if (this.isFloatingWindowOpen) return;
        
        // Create the floating window container
        const floatingWindow = document.createElement('div');
        floatingWindow.id = 'cameraFloatingWindow';
        floatingWindow.className = 'camera-floating-window';
        floatingWindow.innerHTML = `
            <div class="camera-header">
                <span>Reconocimiento de Placas</span>
                <div class="camera-controls">
                    <button id="minimizeFloatingWindow" class="btn-icon" title="Minimizar">
                        <i class="bi bi-dash-lg"></i>
                    </button>
                    <button id="closeFloatingWindow" class="btn-icon" title="Cerrar">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="camera-body">
                <video id="cameraVideo" autoplay playsinline></video>
                <div class="camera-status" id="cameraStatusBar">
                    <span id="plateStatus">Haga clic aquí para activar la cámara</span>
                </div>
            </div>
        `;
        
        // Aplicar la posición guardada si existe
        const savedTop = localStorage.getItem('cameraWindowTop');
        const savedLeft = localStorage.getItem('cameraWindowLeft');
        
        if (savedTop && savedLeft) {
            floatingWindow.style.top = savedTop;
            floatingWindow.style.left = savedLeft;
            // Actualizar también las propiedades del objeto
            this.windowPosition.top = savedTop;
            this.windowPosition.left = savedLeft;
        }
    })
    .catch(error => {
        console.error('Error registering vehicle:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo registrar el vehículo. Inténtelo manualmente.'
        });
    });
}

/**
 * Crea la ventana flotante sin iniciar la cámara
 */
createFloatingWindow() {
    if (this.isFloatingWindowOpen) return;
    
    // Create the floating window container
    const floatingWindow = document.createElement('div');
    floatingWindow.id = 'cameraFloatingWindow';
    floatingWindow.className = 'camera-floating-window';
    floatingWindow.innerHTML = `
        <div class="camera-header">
            <span>Reconocimiento de Placas</span>
            <div class="camera-controls">
                <button id="minimizeFloatingWindow" class="btn-icon" title="Minimizar">
                    <i class="bi bi-dash-lg"></i>
                </button>
                <button id="closeFloatingWindow" class="btn-icon" title="Cerrar">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
        <div class="camera-body">
            <video id="cameraVideo" autoplay playsinline></video>
            <div class="camera-status" id="cameraStatusBar">
                <span id="plateStatus">Haga clic aquí para activar la cámara</span>
            </div>
        </div>
    `;
    
    // Aplicar la posición guardada si existe
    const savedTop = localStorage.getItem('cameraWindowTop');
    const savedLeft = localStorage.getItem('cameraWindowLeft');
    
    if (savedTop && savedLeft) {
        floatingWindow.style.top = savedTop;
        floatingWindow.style.left = savedLeft;
        // Actualizar también las propiedades del objeto
        this.windowPosition.top = savedTop;
        this.windowPosition.left = savedLeft;
    } else if (this.windowPosition.top !== 'auto' && this.windowPosition.left !== 'auto') {
        floatingWindow.style.top = this.windowPosition.top;
        floatingWindow.style.left = this.windowPosition.left;
    }
    
    // Usar el contenedor persistente en lugar del body
    const persistentContainer = document.getElementById('persistentCameraContainer');
    if (persistentContainer) {
        persistentContainer.appendChild(floatingWindow);
        persistentContainer.style.display = 'block'; // Mostrar el contenedor persistente
    } else {
        // Fallback al body si el contenedor persistente no existe
        document.body.appendChild(floatingWindow);
        
        // Añadir evento de clic para activar la cámara solo en la barra de estado
        const statusBar = floatingWindow.querySelector('#cameraStatusBar');
        if (statusBar) {
            statusBar.addEventListener('click', () => {
                if (!this.isActive) {
                    this.startCamera();
                }
            });
        }
    }
    
    /**
     * Open the floating camera window
     * @param {boolean} startCamera - Si se debe iniciar la cámara al abrir la ventana
     */
    openFloatingWindow(startCamera = false) {
        if (this.isFloatingWindowOpen) {
            // Si la ventana ya está abierta pero la cámara no está activa y se solicita iniciarla
            if (!this.isActive && startCamera) {
                this.startCamera();
            }
            return;
        }
        
        // Crear la ventana flotante
        this.createFloatingWindow();
        
        // Iniciar la cámara solo si se solicita explícitamente
        if (startCamera) {
            this.startCamera();
        }
    }

    /**
     * Close the floating camera window
     */
    closeFloatingWindow() {
        if (!this.isFloatingWindowOpen) return;
        
        // Guardar la posición actual antes de cerrar
        if (this.floatingWindow) {
            this.windowPosition = {
                top: this.floatingWindow.style.top,
                left: this.floatingWindow.style.left
            };
            
            // Guardar también en localStorage para persistencia entre páginas
            localStorage.setItem('cameraWindowTop', this.floatingWindow.style.top);
            localStorage.setItem('cameraWindowLeft', this.floatingWindow.style.left);
        }
        
        // Detener la cámara con closeWindow = true
        this.stopCamera(true);
        
        if (this.floatingWindow) {
            document.body.removeChild(this.floatingWindow);
            this.floatingWindow = null;
        }
        
        this.videoElement = null;
        this.isFloatingWindowOpen = false;
        
        // Actualizar el estado en localStorage
        localStorage.setItem('cameraWindowOpen', 'false');
    }

    /**
     * Make the floating window draggable with optimización para mejor rendimiento
     */
    makeFloatingWindowDraggable() {
        const floatingWindow = this.floatingWindow;
        const header = floatingWindow.querySelector('.camera-header');
        
        let isDragging = false;
        let startX, startY, startLeft, startTop;
        
        // Usar requestAnimationFrame para mejor rendimiento
        let rafId = null;
        
        // Función para iniciar el arrastre
        const startDrag = (e) => {
            // Prevenir selección de texto durante el arrastre
            e.preventDefault();
            
            // Obtener posición inicial
            startX = e.clientX || e.touches[0].clientX;
            startY = e.clientY || e.touches[0].clientY;
            
            // Obtener posición inicial de la ventana
            startLeft = floatingWindow.offsetLeft;
            startTop = floatingWindow.offsetTop;
            
            isDragging = true;
            
            // Añadir clase para indicar que se está arrastrando
            floatingWindow.classList.add('dragging');
            
            // Añadir eventos para mover y soltar
            document.addEventListener('mousemove', drag);
            document.addEventListener('touchmove', drag, { passive: false });
            document.addEventListener('mouseup', stopDrag);
            document.addEventListener('touchend', stopDrag);
        };
        
        // Función para realizar el arrastre
        const drag = (e) => {
            if (!isDragging) return;
            
            // Prevenir el comportamiento predeterminado para eventos touch
            if (e.type === 'touchmove') {
                e.preventDefault();
            }
            
            // Calcular nueva posición
            const clientX = e.clientX || e.touches[0].clientX;
            const clientY = e.clientY || e.touches[0].clientY;
            const deltaX = clientX - startX;
            const deltaY = clientY - startY;
            
            // Cancelar cualquier animación pendiente
            if (rafId) {
                cancelAnimationFrame(rafId);
            }
            
            // Usar requestAnimationFrame para actualizar la posición
            rafId = requestAnimationFrame(() => {
                // Calcular nueva posición con límites para no salir de la pantalla
                const newLeft = Math.max(0, Math.min(window.innerWidth - floatingWindow.offsetWidth, startLeft + deltaX));
                const newTop = Math.max(0, Math.min(window.innerHeight - floatingWindow.offsetHeight, startTop + deltaY));
                
                // Aplicar nueva posición
                floatingWindow.style.left = newLeft + 'px';
                floatingWindow.style.top = newTop + 'px';
                
                // Guardar la posición actual
                this.windowPosition = {
                    top: floatingWindow.style.top,
                    left: floatingWindow.style.left
                };
                
                // Guardar también en localStorage para persistencia entre páginas
                localStorage.setItem('cameraWindowTop', floatingWindow.style.top);
                localStorage.setItem('cameraWindowLeft', floatingWindow.style.left);
                
                rafId = null;
            });
        };
        
        // Función para detener el arrastre
        const stopDrag = () => {
            isDragging = false;
            
            // Quitar clase de arrastre
            floatingWindow.classList.remove('dragging');
            
            // Eliminar eventos
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('touchmove', drag);
            document.removeEventListener('mouseup', stopDrag);
            document.removeEventListener('touchend', stopDrag);
            
            // Cancelar cualquier animación pendiente
            if (rafId) {
                cancelAnimationFrame(rafId);
                rafId = null;
            }
        };
        
        // Agregar eventos para iniciar el arrastre
        header.addEventListener('mousedown', startDrag);
        header.addEventListener('touchstart', startDrag, { passive: false });
    }

    /**
     * Load camera settings from localStorage
     */
    loadSettings() {
        const settings = JSON.parse(localStorage.getItem('cameraSettings') || '{}');
        
        this.cameraId = settings.cameraId || '';
        this.resolution = settings.resolution || { width: 640, height: 480 };
        this.scanInterval = settings.scanInterval || 3000;
        this.enableNotifications = settings.enableNotifications !== undefined ? settings.enableNotifications : true;
        this.sensitivity = settings.sensitivity || 5;
        this.autoStart = settings.autoStart !== undefined ? settings.autoStart : true;
        
        // Update UI with loaded settings
        this.updateSettingsUI();
    }

    /**
     * Save camera settings to localStorage
     */
    saveSettings() {
        // Get values from UI
        const cameraSelect = document.getElementById('cameraSelect');
        const resolutionSelect = document.getElementById('resolutionSelect');
        const scanIntervalInput = document.getElementById('scanInterval');
        const enableNotificationsCheckbox = document.getElementById('enableNotifications');
        const sensitivityRange = document.getElementById('sensitivityRange');
        const autoStartCheckbox = document.getElementById('autoStartCamera');
        
        // Update settings
        this.cameraId = cameraSelect.value;
        this.resolution = this.parseResolution(resolutionSelect.value);
        this.scanInterval = parseInt(scanIntervalInput.value) * 1000;
        this.enableNotifications = enableNotificationsCheckbox.checked;
        this.sensitivity = parseInt(sensitivityRange.value);
        this.autoStart = autoStartCheckbox.checked;
        
        // Save to localStorage
        const settings = {
            cameraId: this.cameraId,
            resolution: this.resolution,
            scanInterval: this.scanInterval,
            enableNotifications: this.enableNotifications,
            sensitivity: this.sensitivity,
            autoStart: this.autoStart
        };
        
        localStorage.setItem('cameraSettings', JSON.stringify(settings));
        
        // Show success message
        Swal.fire({
            icon: 'success',
            title: 'Configuración Guardada',
            text: 'La configuración de la cámara ha sido guardada correctamente.',
            timer: 2000,
            timerProgressBar: true
        });
    }

    /**
     * Update the UI with the current settings
     */
    updateSettingsUI() {
        const cameraSelect = document.getElementById('cameraSelect');
        const resolutionSelect = document.getElementById('resolutionSelect');
        const scanIntervalInput = document.getElementById('scanInterval');
        const enableNotificationsCheckbox = document.getElementById('enableNotifications');
        const sensitivityRange = document.getElementById('sensitivityRange');
        const autoStartCheckbox = document.getElementById('autoStartCamera');
        
        if (cameraSelect && this.cameraId) {
            cameraSelect.value = this.cameraId;
        }
        
        if (resolutionSelect) {
            const resolutionStr = `${this.resolution.width}x${this.resolution.height}`;
            resolutionSelect.value = resolutionStr;
        }
        
        if (scanIntervalInput) {
            scanIntervalInput.value = this.scanInterval / 1000;
        }
        
        if (enableNotificationsCheckbox) {
            enableNotificationsCheckbox.checked = this.enableNotifications;
        }
        
        if (sensitivityRange) {
            sensitivityRange.value = this.sensitivity;
        }
        
        if (autoStartCheckbox) {
            autoStartCheckbox.checked = this.autoStart;
        }
    }
}

// Initialize the camera controller when the DOM is loaded


// Initialize the camera controller when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando CameraController...');
    
    // Add CSS for the floating window
    const style = document.createElement('style');
    style.textContent = `
        .camera-floating-window {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 320px;
            height: 280px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            will-change: transform; /* Optimización para mejor rendimiento */
            transform: translateZ(0); /* Forzar aceleración por hardware */
        }
        
        .camera-floating-window.dragging {
            opacity: 0.9;
            cursor: grabbing !important;
        }
        
        .camera-floating-window.minimized {
            height: 40px;
        }
        
        .camera-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background-color: #3f51b5;
            color: white;
            cursor: move;
        }
        
        .camera-controls {
            display: flex;
            gap: 8px;
        }
        
        .btn-icon {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        
        .btn-icon:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .camera-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .camera-body video {
            width: 100%;
            height: calc(100% - 30px);
            object-fit: cover;
            background-color: #000;
        }
        
        .camera-status {
            height: 30px;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 12px;
            font-size: 12px;
            color: #666;
            cursor: pointer;
        }
        
        .camera-status:hover {
            background-color: #e0e0e0;
        }
        
        .camera-floating-window.minimized .camera-body {
            display: none;
        }
    `;
    document.head.appendChild(style);
    
    // Create notification sound
    const createNotificationSound = () => {
        const soundsDir = '../assets/sounds';
        
        // Check if the sounds directory exists, if not create it
        fetch(`${soundsDir}/notification.mp3`)
            .catch(() => {
                // Create the sounds directory
                const xhr = new XMLHttpRequest();
                xhr.open('GET', '../../controladores/create_sounds_directory.php', true);
                xhr.send();
            });
    };
    
    createNotificationSound();
    
    // Verificar compatibilidad del navegador solo si estamos en la página de configuración
    // o si el usuario hace clic en el botón de cámara
    const isConfigPage = document.getElementById('tab8') !== null;
    
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        if (isConfigPage) {
            Swal.fire({
                icon: 'error',
                title: 'Navegador no compatible',
                text: 'Tu navegador no soporta acceso a la cámara. Por favor, utiliza un navegador moderno como Chrome, Firefox o Edge.',
                confirmButtonText: 'Entendido'
            });
        }
    } else {
        // Verificar si ya existe una instancia del controlador
        if (!window.cameraController) {
            console.log('Creando nueva instancia de CameraController');
            
            // Crear una instancia del controlador de cámara
            window.cameraController = new CameraController();
            
            // Inicializar el controlador
            window.cameraController.init().catch(error => {
                console.error('Error al inicializar el controlador de cámara:', error);
            });
            
            // Solo configurar los botones en la página de configuración
            if (isConfigPage) {
                const configPage = document.getElementById('tab8');
                if (configPage) {
                    // No añadimos la sección de permisos ya que no es necesaria
                }
                
                // Add event listeners for the camera configuration page
                const startCameraBtn = document.getElementById('startCameraBtn');
                const stopCameraBtn = document.getElementById('stopCameraBtn');
                const saveSettingsBtn = document.getElementById('saveSettingsBtn');
                const testRecognitionBtn = document.getElementById('testRecognitionBtn');
                
                if (startCameraBtn) {
                    startCameraBtn.addEventListener('click', () => {
                        window.cameraController.startCamera();
                    });
                }
                
                if (stopCameraBtn) {
                    stopCameraBtn.addEventListener('click', () => {
                        window.cameraController.stopCamera();
                    });
                }
                
                if (saveSettingsBtn) {
                    saveSettingsBtn.addEventListener('click', () => {
                        window.cameraController.saveSettings();
                    });
                }
                
                if (testRecognitionBtn) {
                    testRecognitionBtn.addEventListener('click', () => {
                        // Set a test plate for recognition
                        const testPlates = ['ABC-123', 'XYZ-789', 'DEF-456', 'GHI-789'];
                        const randomPlate = testPlates[Math.floor(Math.random() * testPlates.length)];
                        testRecognitionBtn.dataset.testPlate = randomPlate;
                        
                        // Trigger a plate scan
                        window.cameraController.scanForPlate();
                        
                        // Clear the test plate after a short delay
                        setTimeout(() => {
                            delete testRecognitionBtn.dataset.testPlate;
                        }, 1000);
                    });
                }
            }
        } else {
            console.log('CameraController ya existe, verificando estado');
            
            // Si ya existe, verificar si la ventana flotante estaba abierta
            window.cameraController.checkFloatingWindowState();
            
            // Asegurarse de que el botón esté configurado correctamente
            window.cameraController.setupCameraButton();
        }
    }
});
