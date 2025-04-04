/**
 * Persistent Camera Controller for SmartPark
 * Manages camera access across page navigations using a persistent container
 */

class PersistentCameraController {
    constructor() {
        this.videoElement = null;
        this.stream = null;
        this.isActive = false;
        this.isFloatingWindowOpen = false;
        this.floatingWindow = null;
        this.scanInterval = 3000; // Default scan interval in milliseconds
        this.scanIntervalId = null;
        this.lastDetectedPlate = '';
        this.windowPosition = { top: '20px', left: '20px' };
        this.cameraId = '';
        this.resolution = { width: 1280, height: 720 };
        this.autoStart = true;
        this.enableNotifications = true;
        this.sensitivity = 5;
        
        // Bind methods
        this.startCamera = this.startCamera.bind(this);
        this.stopCamera = this.stopCamera.bind(this);
        this.openFloatingWindow = this.openFloatingWindow.bind(this);
        this.closeFloatingWindow = this.closeFloatingWindow.bind(this);
        this.makeFloatingWindowDraggable = this.makeFloatingWindowDraggable.bind(this);
        this.populateCameraDevices = this.populateCameraDevices.bind(this);
        this.saveSettings = this.saveSettings.bind(this);
        this.loadSettings = this.loadSettings.bind(this);
        
        // Cargar configuración guardada
        this.loadSettings();
        
        // Initialize the controller
        this.init();
    }
    
    /**
     * Initialize the camera controller
     */
    init() {
        console.log('Initializing PersistentCameraController');
        
        // Setup camera button in header
        this.setupCameraButton();
        
        // Determinar si estamos en la página de configuración
        const isConfigPage = document.getElementById('tab8') !== null;
        
        console.log('En página de configuración:', isConfigPage);
        
        if (isConfigPage) {
            // En la página de configuración, cargar los dispositivos de cámara
            this.populateCameraDevices();
            
            // Configurar los botones de la página de configuración
            this.setupConfigPageButtons();
        }
        
        // Check if the floating window was previously open
        this.checkFloatingWindowState();
    }
    
    /**
     * Setup the camera button in the header
     */
    setupCameraButton() {
        const cameraBtn = document.getElementById('openCameraBtn');
        if (cameraBtn) {
            cameraBtn.addEventListener('click', () => {
                if (this.isFloatingWindowOpen) {
                    this.closeFloatingWindow();
                } else {
                    this.openFloatingWindow(true); // Start camera when opening from button
                }
            });
            console.log('Camera button setup complete');
        } else {
            console.log('Camera button not found in the DOM');
        }
    }
    
    /**
     * Check if the floating window was previously open and restore it
     */
    checkFloatingWindowState() {
        const wasOpen = localStorage.getItem('cameraWindowOpen') === 'true';
        const wasActive = localStorage.getItem('cameraActive') === 'true';
        const savedTop = localStorage.getItem('cameraWindowTop');
        const savedLeft = localStorage.getItem('cameraWindowLeft');
        
        console.log('Checking camera window state:', { wasOpen, wasActive, savedTop, savedLeft });
        
        if (wasOpen && !this.isFloatingWindowOpen) {
            console.log('Restoring camera window');
            
            // Restore position if available
            if (savedTop && savedLeft) {
                this.windowPosition = { top: savedTop, left: savedLeft };
            }
            
            // Create the floating window
            this.createFloatingWindow();
            
            // Start camera if it was active
            if (wasActive) {
                console.log('Camera was active, restarting...');
                setTimeout(() => {
                    this.startCamera().catch(error => {
                        console.error('Error restarting camera:', error);
                        localStorage.setItem('cameraActive', 'false');
                        this.isActive = false;
                        
                        const statusBar = this.floatingWindow?.querySelector('#cameraStatusBar span');
                        if (statusBar) {
                            statusBar.textContent = 'Error al reiniciar cámara. Haga clic para intentar.';
                        }
                    });
                }, 700);
            }
        }
    }
    
    /**
     * Create the floating camera window
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
        
        // Apply saved position
        floatingWindow.style.top = this.windowPosition.top;
        floatingWindow.style.left = this.windowPosition.left;
        
        // Use the persistent container
        const persistentContainer = document.getElementById('persistentCameraContainer');
        if (persistentContainer) {
            persistentContainer.innerHTML = ''; // Clear any existing content
            persistentContainer.appendChild(floatingWindow);
            persistentContainer.style.display = 'block';
        } else {
            console.error('Persistent camera container not found');
            return;
        }
        
        this.floatingWindow = floatingWindow;
        this.videoElement = document.getElementById('cameraVideo');
        this.isFloatingWindowOpen = true;
        
        // Save window state
        localStorage.setItem('cameraWindowOpen', 'true');
        
        // Make window draggable
        this.makeFloatingWindowDraggable();
        
        // Add event listeners
        document.getElementById('minimizeFloatingWindow').addEventListener('click', () => {
            floatingWindow.classList.toggle('minimized');
        });
        
        document.getElementById('closeFloatingWindow').addEventListener('click', () => {
            this.closeFloatingWindow();
        });
        
        // Add click event to activate camera
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
     * @param {boolean} startCamera - Whether to start the camera when opening
     */
    openFloatingWindow(startCamera = false) {
        if (this.isFloatingWindowOpen) {
            if (!this.isActive && startCamera) {
                this.startCamera();
            }
            return;
        }
        
        this.createFloatingWindow();
        
        if (startCamera) {
            this.startCamera();
        }
    }
    
    /**
     * Close the floating camera window
     */
    closeFloatingWindow() {
        if (!this.isFloatingWindowOpen) return;
        
        // Stop the camera if active
        if (this.isActive) {
            this.stopCamera(false);
        }
        
        // Save window position before closing
        if (this.floatingWindow) {
            localStorage.setItem('cameraWindowTop', this.floatingWindow.style.top);
            localStorage.setItem('cameraWindowLeft', this.floatingWindow.style.left);
            
            // Hide the persistent container
            const persistentContainer = document.getElementById('persistentCameraContainer');
            if (persistentContainer) {
                persistentContainer.style.display = 'none';
            }
        }
        
        this.isFloatingWindowOpen = false;
        localStorage.setItem('cameraWindowOpen', 'false');
    }
    
    /**
     * Make the floating window draggable
     */
    makeFloatingWindowDraggable() {
        if (!this.floatingWindow) return;
        
        const header = this.floatingWindow.querySelector('.camera-header');
        let isDragging = false;
        let offsetX, offsetY;
        
        // Start drag
        const startDrag = (e) => {
            // Only allow dragging from the header, not the control buttons
            if (e.target.closest('.camera-controls')) return;
            
            isDragging = true;
            
            // Calculate the offset from the mouse position to the window corner
            const rect = this.floatingWindow.getBoundingClientRect();
            offsetX = e.clientX - rect.left;
            offsetY = e.clientY - rect.top;
            
            // Add event listeners for dragging
            document.addEventListener('mousemove', drag);
            document.addEventListener('mouseup', stopDrag);
            
            // Prevent text selection during drag
            e.preventDefault();
        };
        
        // Perform drag
        const drag = (e) => {
            if (!isDragging) return;
            
            // Calculate new position
            const newLeft = e.clientX - offsetX;
            const newTop = e.clientY - offsetY;
            
            // Apply new position
            this.floatingWindow.style.left = `${newLeft}px`;
            this.floatingWindow.style.top = `${newTop}px`;
            
            // Update position in object
            this.windowPosition.left = `${newLeft}px`;
            this.windowPosition.top = `${newTop}px`;
        };
        
        // Stop drag
        const stopDrag = () => {
            if (!isDragging) return;
            
            isDragging = false;
            
            // Remove event listeners
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('mouseup', stopDrag);
            
            // Save position to localStorage
            localStorage.setItem('cameraWindowTop', this.floatingWindow.style.top);
            localStorage.setItem('cameraWindowLeft', this.floatingWindow.style.left);
        };
        
        // Add event listener to header
        if (header) {
            header.addEventListener('mousedown', startDrag);
        }
    }
    
    /**
     * Start the camera
     */
    async startCamera() {
        if (this.isActive) return;
        
        try {
            // Request camera permission
            const constraints = {
                video: {
                    deviceId: this.cameraId ? { exact: this.cameraId } : undefined,
                    width: { ideal: this.resolution.width },
                    height: { ideal: this.resolution.height }
                }
            };
            
            console.log('Iniciando cámara con configuración:', constraints);
            
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            
            // Set the stream to the video element
            this.videoElement.srcObject = stream;
            this.stream = stream;
            this.isActive = true;
            
            // Update status
            const statusBar = this.floatingWindow.querySelector('#cameraStatusBar span');
            if (statusBar) {
                statusBar.textContent = 'Cámara activa';
            }
            
            // Save camera state
            localStorage.setItem('cameraActive', 'true');
            
            console.log('Camera started successfully');
            
            // Actualizar el último dispositivo usado
            const tracks = stream.getVideoTracks();
            if (tracks.length > 0) {
                const settings = tracks[0].getSettings();
                if (settings.deviceId) {
                    this.cameraId = settings.deviceId;
                    localStorage.setItem('cameraId', settings.deviceId);
                }
            }
        } catch (error) {
            console.error('Error starting camera:', error);
            
            // Update status
            const statusBar = this.floatingWindow.querySelector('#cameraStatusBar span');
            if (statusBar) {
                statusBar.textContent = 'Error al iniciar cámara. Haga clic para intentar.';
            }
            
            // Reset state
            this.isActive = false;
            localStorage.setItem('cameraActive', 'false');
        }
    }
    
    /**
     * Stop the camera
     * @param {boolean} closeWindow - Whether to close the window after stopping
     */
    stopCamera(closeWindow = true) {
        if (!this.isActive) return;
        
        // Stop all tracks
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
        
        // Clear video source
        if (this.videoElement) {
            this.videoElement.srcObject = null;
        }
        
        // Update status
        this.isActive = false;
        localStorage.setItem('cameraActive', 'false');
        
        const statusBar = this.floatingWindow?.querySelector('#cameraStatusBar span');
        if (statusBar) {
            statusBar.textContent = 'Haga clic aquí para activar la cámara';
        }
        
        console.log('Camera stopped');
        
        // Close window if requested
        if (closeWindow) {
            this.closeFloatingWindow();
        }
    }
}

// Add CSS for the floating window
const addCameraStyles = () => {
    const style = document.createElement('style');
    style.textContent = `
        .camera-floating-window {
            position: fixed;
            width: 256px; /* Reducido en 20% de 320px */
            height: 224px; /* Reducido en 20% de 280px */
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            z-index: 9999;
            display: flex;
            flex-direction: column;
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
        }
        
        .camera-body {
            flex: 1;
            position: relative;
            background-color: #000;
        }
        
        #cameraVideo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .camera-status {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 8px;
            text-align: center;
            font-size: 14px;
            color: #666;
            cursor: pointer;
        }
        
        .camera-status:hover {
            background-color: #e0e0e0;
        }
        
        .camera-floating-window.minimized {
            height: auto;
        }
        
        .camera-floating-window.minimized .camera-body {
            display: none;
        }
        
        .persistent-camera-container {
            position: fixed;
            z-index: 9999;
        }
    `;
    document.head.appendChild(style);
};

// Initialize the persistent camera controller when the DOM is loaded
/**
 * Populate the camera devices dropdown
 */
PersistentCameraController.prototype.populateCameraDevices = async function() {
    const cameraSelect = document.getElementById('cameraSelect');
    if (!cameraSelect) return;
    
    try {
        // Request permission to get device labels
        await navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                // Stop the stream immediately after getting permission
                stream.getTracks().forEach(track => track.stop());
            });
        
        // Get all video input devices
        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = devices.filter(device => device.kind === 'videoinput');
        
        // Clear the select options
        cameraSelect.innerHTML = '';
        
        // Add options for each video device
        videoDevices.forEach(device => {
            const option = document.createElement('option');
            option.value = device.deviceId;
            option.text = device.label || `Camera ${cameraSelect.options.length + 1}`;
            cameraSelect.appendChild(option);
            
            // Select the saved camera if available
            if (device.deviceId === this.cameraId) {
                option.selected = true;
            }
        });
        
        console.log(`Found ${videoDevices.length} camera devices`);
    } catch (error) {
        console.error('Error populating camera devices:', error);
    }
};

/**
 * Setup the buttons on the configuration page
 */
PersistentCameraController.prototype.setupConfigPageButtons = function() {
    const startCameraBtn = document.getElementById('startCameraBtn');
    const stopCameraBtn = document.getElementById('stopCameraBtn');
    const saveSettingsBtn = document.getElementById('saveSettingsBtn');
    const testRecognitionBtn = document.getElementById('testRecognitionBtn');
    const autoStartCheckbox = document.getElementById('autoStartCamera');
    const enableNotificationsCheckbox = document.getElementById('enableNotifications');
    const sensitivityRange = document.getElementById('sensitivityRange');
    const resolutionSelect = document.getElementById('resolutionSelect');
    const cameraSelect = document.getElementById('cameraSelect');
    
    // Set initial values from settings
    if (autoStartCheckbox) autoStartCheckbox.checked = this.autoStart;
    if (enableNotificationsCheckbox) enableNotificationsCheckbox.checked = this.enableNotifications;
    if (sensitivityRange) sensitivityRange.value = this.sensitivity;
    
    // Start camera button
    if (startCameraBtn) {
        startCameraBtn.addEventListener('click', () => {
            this.openFloatingWindow(true);
        });
    }
    
    // Stop camera button
    if (stopCameraBtn) {
        stopCameraBtn.addEventListener('click', () => {
            this.stopCamera(false);
        });
    }
    
    // Save settings button
    if (saveSettingsBtn) {
        saveSettingsBtn.addEventListener('click', () => {
            // Update settings from form
            if (cameraSelect) this.cameraId = cameraSelect.value;
            if (autoStartCheckbox) this.autoStart = autoStartCheckbox.checked;
            if (enableNotificationsCheckbox) this.enableNotifications = enableNotificationsCheckbox.checked;
            if (sensitivityRange) this.sensitivity = parseInt(sensitivityRange.value);
            if (resolutionSelect) {
                const [width, height] = resolutionSelect.value.split('x').map(Number);
                this.resolution = { width, height };
            }
            
            // Save settings
            this.saveSettings();
            
            // Show confirmation
            Swal.fire({
                icon: 'success',
                title: 'Configuración guardada',
                text: 'La configuración de la cámara ha sido guardada correctamente.',
                confirmButtonText: 'Aceptar'
            });
        });
    }
    
    // Test recognition button
    if (testRecognitionBtn) {
        testRecognitionBtn.addEventListener('click', () => {
            // Simulate a plate detection
            const testPlates = ['ABC-123', 'XYZ-789', 'DEF-456', 'GHI-789'];
            const randomPlate = testPlates[Math.floor(Math.random() * testPlates.length)];
            
            // Update the last detected plate
            const lastDetectedPlateInput = document.getElementById('lastDetectedPlate');
            if (lastDetectedPlateInput) {
                lastDetectedPlateInput.value = randomPlate;
            }
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Placa detectada',
                text: `Se ha detectado la placa: ${randomPlate}`,
                confirmButtonText: 'Aceptar'
            });
        });
    }
};

/**
 * Load camera settings from localStorage
 */
PersistentCameraController.prototype.loadSettings = function() {
    this.cameraId = localStorage.getItem('cameraId') || '';
    this.autoStart = localStorage.getItem('autoStart') === 'true';
    this.enableNotifications = localStorage.getItem('enableNotifications') !== 'false';
    this.sensitivity = parseInt(localStorage.getItem('sensitivity') || '5');
    
    const savedWidth = parseInt(localStorage.getItem('resolutionWidth') || '1280');
    const savedHeight = parseInt(localStorage.getItem('resolutionHeight') || '720');
    this.resolution = { width: savedWidth, height: savedHeight };
    
    console.log('Configuración cargada:', {
        cameraId: this.cameraId,
        autoStart: this.autoStart,
        enableNotifications: this.enableNotifications,
        sensitivity: this.sensitivity,
        resolution: this.resolution
    });
};

/**
 * Save camera settings to localStorage
 */
PersistentCameraController.prototype.saveSettings = function() {
    localStorage.setItem('cameraId', this.cameraId);
    localStorage.setItem('autoStart', this.autoStart);
    localStorage.setItem('enableNotifications', this.enableNotifications);
    localStorage.setItem('sensitivity', this.sensitivity);
    localStorage.setItem('resolutionWidth', this.resolution.width);
    localStorage.setItem('resolutionHeight', this.resolution.height);
    
    console.log('Configuración guardada:', {
        cameraId: this.cameraId,
        autoStart: this.autoStart,
        enableNotifications: this.enableNotifications,
        sensitivity: this.sensitivity,
        resolution: this.resolution
    });
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing PersistentCameraController...');
    
    // Add CSS styles
    addCameraStyles();
    
    // Create controller instance if it doesn't exist
    if (!window.persistentCameraController) {
        window.persistentCameraController = new PersistentCameraController();
    }
});

// For pages that load after DOMContentLoaded has already fired
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    if (!window.persistentCameraController) {
        console.log('Page already loaded, initializing PersistentCameraController now...');
        addCameraStyles();
        window.persistentCameraController = new PersistentCameraController();
    }
}
