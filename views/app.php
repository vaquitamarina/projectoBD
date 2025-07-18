<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEMA TRANSPORTE</title>
    <script src="https://kit.fontawesome.com/f9a3e96628.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/crud.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .form-section { background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; }
        .btn-success { background: #28a745; }
        .hidden { display: none; }
        .asientos-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 20px 0; }
        .asiento { padding: 10px; border: 2px solid #ddd; text-align: center; cursor: pointer; border-radius: 4px; }
        .asiento.disponible { background: #28a745; color: white; }
        .asiento.ocupado { background: #dc3545; color: white; cursor: not-allowed; }
        .asiento.seleccionado { background: #007bff; color: white; }
        .pasajero-form { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 4px; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>

<body>
    <div class="container">
        <h1>Sistema de Transporte - Compra de Tickets</h1>
        
        <!-- Sección de Login/Registro -->
        <div class="form-section" id="loginSection">
            <h2>Iniciar Sesión / Registrarse</h2>
            
            <div id="loginForm">
                <h3>Iniciar Sesión</h3>
                <form id="loginFormElement">
                    <div class="form-group">
                        <label for="loginUsername">Username:</label>
                        <input type="text" id="loginUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="loginPassword">Contraseña:</label>
                        <input type="password" id="loginPassword" name="password" required>
                    </div>
                    <button type="submit" class="btn">Iniciar Sesión</button>
                    <button type="button" class="btn btn-secondary" onclick="mostrarRegistro()">Registrarse</button>
                </form>
            </div>
            
            <div id="registroForm" class="hidden">
                <h3>Registrarse</h3>
                <form id="registroFormElement">
                    <div class="form-group">
                        <label for="regUsername">Username:</label>
                        <input type="text" id="regUsername" name="username" maxlength="50" required>
                    </div>
                    <div class="form-group">
                        <label for="regEmail">Email:</label>
                        <input type="email" id="regEmail" name="email" maxlength="100" required>
                    </div>
                    <div class="form-group">
                        <label for="regPassword">Contraseña:</label>
                        <input type="password" id="regPassword" name="password" maxlength="10" required>
                        <small>Máximo 10 caracteres</small>
                    </div>
                    <div class="form-group">
                        <label for="regConfirmarPassword">Confirmar Contraseña:</label>
                        <input type="password" id="regConfirmarPassword" name="confirmar_password" maxlength="10" required>
                    </div>
                    <button type="submit" class="btn">Registrarse</button>
                    <button type="button" class="btn btn-secondary" onclick="mostrarLogin()">Volver al Login</button>
                </form>
            </div>
        </div>
        
        <!-- Sección de Búsqueda de Viajes -->
        <div class="form-section hidden" id="busquedaSection">
            <h2>Buscar Viajes</h2>
            <form id="busquedaForm">
                <div class="form-group">
                    <label for="ciudadOrigen">Ciudad de Origen:</label>
                    <select id="ciudadOrigen" name="ciudadOrigen" required>
                        <option value="">Seleccione ciudad</option>
                        <option value="Lima">Lima</option>
                        <option value="Arequipa">Arequipa</option>
                        <option value="Cusco">Cusco</option>
                        <option value="Trujillo">Trujillo</option>
                        <option value="Chiclayo">Chiclayo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="ciudadDestino">Ciudad de Destino:</label>
                    <select id="ciudadDestino" name="ciudadDestino" required>
                        <option value="">Seleccione ciudad</option>
                        <option value="Lima">Lima</option>
                        <option value="Arequipa">Arequipa</option>
                        <option value="Cusco">Cusco</option>
                        <option value="Trujillo">Trujillo</option>
                        <option value="Chiclayo">Chiclayo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fechaViaje">Fecha de Viaje:</label>
                    <input type="date" id="fechaViaje" name="fechaViaje" required>
                </div>
                <button type="submit" class="btn">Buscar Viajes</button>
            </form>
        </div>
        
        <!-- Sección de Resultados de Viajes -->
        <div class="form-section hidden" id="resultadosSection">
            <h2>Viajes Disponibles</h2>
            <div id="listaViajes">
                <!-- Los viajes se cargarán aquí dinámicamente -->
            </div>
        </div>
        
        <!-- Sección de Selección de Asientos -->
        <div class="form-section hidden" id="asientosSection">
            <h2>Seleccionar Asientos</h2>
            <div id="infoViaje">
                <!-- Información del viaje seleccionado -->
            </div>
            <div class="asientos-grid" id="asientosGrid">
                <!-- Los asientos se cargarán aquí dinámicamente -->
            </div>
            <button type="button" class="btn" onclick="continuarCompra()">Continuar con la Compra</button>
        </div>
        
        <!-- Sección de Datos de Pasajeros -->
        <div class="form-section hidden" id="pasajerosSection">
            <h2>Datos de Pasajeros</h2>
            <div id="formasPasajeros">
                <!-- Los formularios de pasajeros se generarán aquí dinámicamente -->
            </div>
            <button type="button" class="btn btn-success" onclick="confirmarCompra()">Confirmar Compra</button>
        </div>
        
        <!-- Sección de Confirmación -->
        <div class="form-section hidden" id="confirmacionSection">
            <h2>Confirmación de Compra</h2>
            <div id="resumenCompra">
                <!-- Resumen de la compra -->
            </div>
        </div>
        
        <!-- Área de mensajes -->
        <div id="mensajes"></div>
    </div>

    <script>
        let viajeSeleccionado = null;
        let asientosSeleccionados = [];
        let usuarioActual = null;
        
        // Funciones de autenticación
        function mostrarRegistro() {
            document.getElementById('loginForm').classList.add('hidden');
            document.getElementById('registroForm').classList.remove('hidden');
        }
        
        function mostrarLogin() {
            document.getElementById('registroForm').classList.add('hidden');
            document.getElementById('loginForm').classList.remove('hidden');
        }
        
        function mostrarSeccion(seccionId) {
            const secciones = ['loginSection', 'busquedaSection', 'resultadosSection', 'asientosSection', 'pasajerosSection', 'confirmacionSection'];
            secciones.forEach(id => {
                document.getElementById(id).classList.add('hidden');
            });
            document.getElementById(seccionId).classList.remove('hidden');
        }
        
        function mostrarMensaje(mensaje, tipo = 'success') {
            const mensajesDiv = document.getElementById('mensajes');
            mensajesDiv.innerHTML = `<div class="message ${tipo}">${mensaje}</div>`;
            setTimeout(() => mensajesDiv.innerHTML = '', 5000);
        }
        
        // Event listeners para formularios
        document.getElementById('loginFormElement').addEventListener('submit', async function(e) {
            e.preventDefault();
            const username = document.getElementById('loginUsername').value;
            const password = document.getElementById('loginPassword').value;
            
            try {
                const formData = new FormData();
                formData.append('username', username);
                formData.append('password', password);
                
                const response = await fetch('../api.php?action=login', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    usuarioActual = result.usuario;
                    mostrarMensaje(result.message, 'success');
                    mostrarSeccion('busquedaSection');
                } else {
                    mostrarMensaje(result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión', 'error');
            }
        });
        
        document.getElementById('registroFormElement').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch('../api.php?action=registro', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    mostrarMensaje(result.message, 'success');
                    mostrarLogin();
                } else {
                    mostrarMensaje(result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión', 'error');
            }
        });
        
        document.getElementById('busquedaForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const origen = document.getElementById('ciudadOrigen').value;
            const destino = document.getElementById('ciudadDestino').value;
            const fecha = document.getElementById('fechaViaje').value;
            
            // Simular búsqueda de viajes
            const viajes = [
                { id: 1, origen: origen, destino: destino, fecha: fecha, hora: '08:00', precio: 50, bus: 'Bus Premium' },
                { id: 2, origen: origen, destino: destino, fecha: fecha, hora: '14:00', precio: 45, bus: 'Bus Económico' },
                { id: 3, origen: origen, destino: destino, fecha: fecha, hora: '20:00', precio: 55, bus: 'Bus VIP' }
            ];
            
            mostrarViajes(viajes);
            mostrarSeccion('resultadosSection');
        });
        
        function mostrarViajes(viajes) {
            const listaDiv = document.getElementById('listaViajes');
            listaDiv.innerHTML = '';
            
            viajes.forEach(viaje => {
                const viajeDiv = document.createElement('div');
                viajeDiv.style.border = '1px solid #ddd';
                viajeDiv.style.padding = '15px';
                viajeDiv.style.margin = '10px 0';
                viajeDiv.style.borderRadius = '4px';
                
                viajeDiv.innerHTML = `
                    <h3>${viaje.origen} → ${viaje.destino}</h3>
                    <p><strong>Fecha:</strong> ${viaje.fecha}</p>
                    <p><strong>Hora:</strong> ${viaje.hora}</p>
                    <p><strong>Bus:</strong> ${viaje.bus}</p>
                    <p><strong>Precio:</strong> S/ ${viaje.precio}</p>
                    <button class="btn" onclick="seleccionarViaje(${viaje.id}, '${viaje.origen}', '${viaje.destino}', '${viaje.fecha}', '${viaje.hora}', ${viaje.precio}, '${viaje.bus}')">Seleccionar</button>
                `;
                
                listaDiv.appendChild(viajeDiv);
            });
        }
        
        function seleccionarViaje(id, origen, destino, fecha, hora, precio, bus) {
            viajeSeleccionado = { id, origen, destino, fecha, hora, precio, bus };
            
            // Mostrar información del viaje
            document.getElementById('infoViaje').innerHTML = `
                <h3>Viaje Seleccionado</h3>
                <p><strong>Ruta:</strong> ${origen} → ${destino}</p>
                <p><strong>Fecha:</strong> ${fecha} a las ${hora}</p>
                <p><strong>Bus:</strong> ${bus}</p>
                <p><strong>Precio por asiento:</strong> S/ ${precio}</p>
            `;
            
            // Generar asientos simulados
            generarAsientos();
            mostrarSeccion('asientosSection');
        }
        
        function generarAsientos() {
            const grid = document.getElementById('asientosGrid');
            grid.innerHTML = '';
            
            // Simular 40 asientos (10 filas x 4 columnas)
            for (let i = 1; i <= 40; i++) {
                const asiento = document.createElement('div');
                asiento.className = 'asiento disponible';
                asiento.textContent = i;
                asiento.setAttribute('data-asiento', i);
                
                // Simular algunos asientos ocupados
                if ([5, 12, 18, 25, 33].includes(i)) {
                    asiento.className = 'asiento ocupado';
                } else {
                    asiento.addEventListener('click', function() {
                        toggleAsiento(i);
                    });
                }
                
                grid.appendChild(asiento);
            }
        }
        
        function toggleAsiento(numero) {
            const asiento = document.querySelector(`[data-asiento="${numero}"]`);
            
            if (asiento.classList.contains('seleccionado')) {
                asiento.classList.remove('seleccionado');
                asiento.classList.add('disponible');
                asientosSeleccionados = asientosSeleccionados.filter(a => a !== numero);
            } else {
                asiento.classList.remove('disponible');
                asiento.classList.add('seleccionado');
                asientosSeleccionados.push(numero);
            }
        }
        
        function continuarCompra() {
            if (asientosSeleccionados.length === 0) {
                mostrarMensaje('Debe seleccionar al menos un asiento', 'error');
                return;
            }
            
            generarFormulariosPasajeros();
            mostrarSeccion('pasajerosSection');
        }
        
        function generarFormulariosPasajeros() {
            const container = document.getElementById('formasPasajeros');
            container.innerHTML = '';
            
            asientosSeleccionados.forEach((asiento, index) => {
                const form = document.createElement('div');
                form.className = 'pasajero-form';
                form.innerHTML = `
                    <h4>Pasajero ${index + 1} - Asiento ${asiento}</h4>
                    <div class="form-group">
                        <label>Nombres:</label>
                        <input type="text" name="nombres_${index}" required>
                    </div>
                    <div class="form-group">
                        <label>Apellidos:</label>
                        <input type="text" name="apellidos_${index}" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email_${index}" required>
                    </div>
                    <div class="form-group">
                        <label>DNI:</label>
                        <input type="text" name="dni_${index}" pattern="[0-9]{8}" required>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Nacimiento:</label>
                        <input type="date" name="fechaNacimiento_${index}" required>
                    </div>
                    <div class="form-group">
                        <label>Sexo:</label>
                        <select name="sexo_${index}" required>
                            <option value="">Seleccione</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>
                `;
                container.appendChild(form);
            });
        }
        
        function confirmarCompra() {
            // Validar que todos los campos estén completos
            const inputs = document.querySelectorAll('#formasPasajeros input, #formasPasajeros select');
            let valid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.style.borderColor = 'red';
                } else {
                    input.style.borderColor = '#ddd';
                }
            });
            
            if (!valid) {
                mostrarMensaje('Por favor complete todos los campos', 'error');
                return;
            }
            
            // Simular confirmación de compra
            const total = viajeSeleccionado.precio * asientosSeleccionados.length;
            
            document.getElementById('resumenCompra').innerHTML = `
                <h3>¡Compra Realizada Exitosamente!</h3>
                <p><strong>Viaje:</strong> ${viajeSeleccionado.origen} → ${viajeSeleccionado.destino}</p>
                <p><strong>Fecha:</strong> ${viajeSeleccionado.fecha} a las ${viajeSeleccionado.hora}</p>
                <p><strong>Asientos:</strong> ${asientosSeleccionados.join(', ')}</p>
                <p><strong>Total pagado:</strong> S/ ${total}</p>
                <p><strong>Código de reserva:</strong> TR${Date.now()}</p>
                <button class="btn" onclick="location.reload()">Nueva Compra</button>
            `;
            
            mostrarSeccion('confirmacionSection');
        }
        
        // Configurar fecha mínima para el input de fecha
        document.getElementById('fechaViaje').min = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>