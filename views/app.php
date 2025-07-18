<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEMA TRANSPORTE</title>
    <script src="https://kit.fontawesome.com/f9a3e96628.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/app.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
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
                        <!-- Las ciudades se cargarán dinámicamente -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="ciudadDestino">Ciudad de Destino:</label>
                    <select id="ciudadDestino" name="ciudadDestino" required>
                        <option value="">Seleccione ciudad</option>
                        <!-- Las ciudades se cargarán dinámicamente -->
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
        <footer>
            <a class="button-return" href="../index.php">Volver</a>
        </footer>
    </div>

    <script>
        let viajeSeleccionado = null;
        let asientosSeleccionados = [];
        let usuarioActual = null;
        
        // Cargar ciudades al inicio
        async function cargarCiudades() {
            try {
                const response = await fetch('../api.php?action=obtener_ciudades');
                const result = await response.json();
                
                if (result.success) {
                    const ciudades = result.ciudades;
                    const origenSelect = document.getElementById('ciudadOrigen');
                    const destinoSelect = document.getElementById('ciudadDestino');
                    
                    // Limpiar opciones existentes excepto la primera
                    origenSelect.innerHTML = '<option value="">Seleccione ciudad</option>';
                    destinoSelect.innerHTML = '<option value="">Seleccione ciudad</option>';
                    
                    // Agregar las ciudades de la base de datos
                    ciudades.forEach(ciudad => {
                        const option1 = document.createElement('option');
                        option1.value = ciudad.ciudad;
                        option1.textContent = ciudad.ciudad;
                        origenSelect.appendChild(option1);
                        
                        const option2 = document.createElement('option');
                        option2.value = ciudad.ciudad;
                        option2.textContent = ciudad.ciudad;
                        destinoSelect.appendChild(option2);
                    });
                }
            } catch (error) {
                console.error('Error cargando ciudades:', error);
            }
        }
        
        // Cargar ciudades cuando se muestra la sección de búsqueda
        document.addEventListener('DOMContentLoaded', function() {
            cargarCiudades();
        });
        
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
        
        document.getElementById('busquedaForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const origen = document.getElementById('ciudadOrigen').value;
            const destino = document.getElementById('ciudadDestino').value;
            const fecha = document.getElementById('fechaViaje').value;
            
            if (origen === destino) {
                mostrarMensaje('La ciudad de origen y destino no pueden ser iguales', 'error');
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('ciudadOrigen', origen);
                formData.append('ciudadDestino', destino);
                formData.append('fechaViaje', fecha);
                
                const response = await fetch('../api.php?action=buscar_viajes', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    if (result.viajes.length > 0) {
                        mostrarViajes(result.viajes);
                        mostrarSeccion('resultadosSection');
                    } else {
                        mostrarMensaje('No hay viajes disponibles para la fecha y ruta seleccionadas', 'error');
                    }
                } else {
                    mostrarMensaje(result.message || 'Error al buscar viajes', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión al buscar viajes', 'error');
            }
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
                    <h3>${viaje.ciudadOrigen} → ${viaje.ciudadFinal}</h3>
                    <p><strong>Fecha:</strong> ${viaje.fecha}</p>
                    <p><strong>Hora de salida:</strong> ${viaje.hInicio}</p>
                    <p><strong>Hora de llegada:</strong> ${viaje.hFinal}</p>
                    <p><strong>Bus:</strong> ${viaje.placa} (${viaje.clase})</p>
                    <p><strong>Precio:</strong> S/ ${viaje.precio}</p>
                    <p><strong>Asientos disponibles:</strong> ${viaje.asientos_disponibles}</p>
                    <p><strong>Tiempo de viaje:</strong> ${viaje.tiempoViaje}</p>
                    <button class="btn" onclick="seleccionarViaje(${viaje.idViaje}, '${viaje.ciudadOrigen}', '${viaje.ciudadFinal}', '${viaje.fecha}', '${viaje.hInicio}', '${viaje.hFinal}', ${viaje.precio}, '${viaje.placa}', '${viaje.clase}')">Seleccionar</button>
                `;
                
                listaDiv.appendChild(viajeDiv);
            });
        }
        
        function seleccionarViaje(idViaje, origen, destino, fecha, hInicio, hFinal, precio, placa, clase) {
            viajeSeleccionado = { 
                id: idViaje, 
                origen: origen, 
                destino: destino, 
                fecha: fecha, 
                hInicio: hInicio, 
                hFinal: hFinal, 
                precio: precio, 
                placa: placa, 
                clase: clase 
            };
            
            // Mostrar información del viaje
            document.getElementById('infoViaje').innerHTML = `
                <h3>Viaje Seleccionado</h3>
                <p><strong>Ruta:</strong> ${origen} → ${destino}</p>
                <p><strong>Fecha:</strong> ${fecha}</p>
                <p><strong>Salida:</strong> ${hInicio}</p>
                <p><strong>Llegada:</strong> ${hFinal}</p>
                <p><strong>Bus:</strong> ${placa} (${clase})</p>
                <p><strong>Precio por asiento:</strong> S/ ${precio}</p>
            `;
            
            // Cargar asientos reales
            cargarAsientos(idViaje);
            mostrarSeccion('asientosSection');
        }
        
        async function cargarAsientos(idViaje) {
            try {
                const formData = new FormData();
                formData.append('idViaje', idViaje);
                
                const response = await fetch('../api.php?action=obtener_asientos', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    generarAsientos(result.asientos);
                } else {
                    mostrarMensaje('Error al cargar asientos: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión al cargar asientos', 'error');
            }
        }
        
        function generarAsientos(asientos) {
            const grid = document.getElementById('asientosGrid');
            grid.innerHTML = '';
            
            asientos.forEach(asiento => {
                const asientoDiv = document.createElement('div');
                asientoDiv.className = `asiento ${asiento.disponibilidad}`;
                asientoDiv.textContent = asiento.numeroAsiento;
                asientoDiv.setAttribute('data-asiento', asiento.idAsiento);
                asientoDiv.setAttribute('data-numero', asiento.numeroAsiento);
                
                if (asiento.disponibilidad === 'disponible') {
                    asientoDiv.addEventListener('click', function() {
                        toggleAsiento(asiento.idAsiento, asiento.numeroAsiento);
                    });
                }
                
                grid.appendChild(asientoDiv);
            });
        }
        
        function toggleAsiento(idAsiento, numeroAsiento) {
            const asiento = document.querySelector(`[data-asiento="${idAsiento}"]`);
            
            if (asiento.classList.contains('seleccionado')) {
                asiento.classList.remove('seleccionado');
                asiento.classList.add('disponible');
                asientosSeleccionados = asientosSeleccionados.filter(a => a.id !== idAsiento);
            } else {
                asiento.classList.remove('disponible');
                asiento.classList.add('seleccionado');
                asientosSeleccionados.push({
                    id: idAsiento,
                    numero: numeroAsiento
                });
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
                    <h4>Pasajero ${index + 1} - Asiento ${asiento.numero}</h4>
                    <input type="hidden" name="idAsiento_${index}" value="${asiento.id}">
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
            
            // Procesar compra real
            procesarCompraReal();
        }
        
        async function procesarCompraReal() {
            // Mostrar indicador de carga
            mostrarMensaje('Procesando compra...', 'success');
            
            try {
                const formData = new FormData();
                formData.append('idViaje', viajeSeleccionado.id);
                
                // Recopilar datos de pasajeros
                const pasajeros = [];
                const asientos = [];
                
                asientosSeleccionados.forEach((asiento, index) => {
                    const pasajero = {
                        nombres: document.querySelector(`input[name="nombres_${index}"]`).value,
                        apellidos: document.querySelector(`input[name="apellidos_${index}"]`).value,
                        email: document.querySelector(`input[name="email_${index}"]`).value,
                        dni: document.querySelector(`input[name="dni_${index}"]`).value,
                        fechaNacimiento: document.querySelector(`input[name="fechaNacimiento_${index}"]`).value,
                        sexo: document.querySelector(`select[name="sexo_${index}"]`).value
                    };
                    
                    pasajeros.push(pasajero);
                    asientos.push(asiento.id);
                });
                
                formData.append('pasajeros', JSON.stringify(pasajeros));
                formData.append('asientos', JSON.stringify(asientos));
                
                const response = await fetch('../api.php?action=procesar_compra', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    mostrarConfirmacionCompra(result);
                } else {
                    mostrarMensaje(result.message || 'Error al procesar la compra', 'error');
                }
                
            } catch (error) {
                console.error('Error en procesarCompraReal:', error);
                mostrarMensaje('Error de conexión: ' + error.message, 'error');
            }
        }
        
        function mostrarConfirmacionCompra(result) {
            const total = viajeSeleccionado.precio * asientosSeleccionados.length;
            const numerosAsientos = asientosSeleccionados.map(a => a.numero).join(', ');
            
            let ticketsHtml = '';
            result.tickets.forEach(ticket => {
                ticketsHtml += `<p><strong>Ticket #${ticket.idTicket}</strong> - ${ticket.nombres} ${ticket.apellidos}</p>`;
            });
            
            document.getElementById('resumenCompra').innerHTML = `
                <h3>¡Compra Realizada Exitosamente!</h3>
                <p><strong>Viaje:</strong> ${viajeSeleccionado.origen} → ${viajeSeleccionado.destino}</p>
                <p><strong>Fecha:</strong> ${viajeSeleccionado.fecha}</p>
                <p><strong>Salida:</strong> ${viajeSeleccionado.hInicio}</p>
                <p><strong>Llegada:</strong> ${viajeSeleccionado.hFinal}</p>
                <p><strong>Bus:</strong> ${viajeSeleccionado.placa} (${viajeSeleccionado.clase})</p>
                <p><strong>Asientos:</strong> ${numerosAsientos}</p>
                <p><strong>Total pagado:</strong> S/ ${total}</p>
                <p><strong>Código de reserva:</strong> ${result.codigoReserva}</p>
                <h4>Tickets generados:</h4>
                ${ticketsHtml}
                <p><em>Los pasajeros y tickets han sido guardados en la base de datos</em></p>
                <button class="btn" onclick="location.reload()">Nueva Compra</button>
            `;
            
            mostrarSeccion('confirmacionSection');
        }
        
        // Configurar fecha mínima para el input de fecha
        document.getElementById('fechaViaje').min = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>