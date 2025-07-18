<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Transporte</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <header class="dashboard-header">
            <h1>Bienvenido, <?php echo htmlspecialchars($usuario['username'] ?? 'Usuario'); ?>!</h1>
            <nav>
                <a href="?action=comprarTicket">Comprar Ticket</a>
                <a href="?action=actualizarPerfil">Mi Perfil</a>
                <a href="?action=logout">Cerrar Sesión</a>
            </nav>
        </header>
        
        <?php if (isset($_GET['compra']) && $_GET['compra'] === 'exitosa'): ?>
            <div class="success-message">
                Compra realizada exitosamente! Ticket #<?php echo htmlspecialchars($_GET['ticket']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['perfil']) && $_GET['perfil'] === 'actualizado'): ?>
            <div class="success-message">
                Perfil actualizado exitosamente.
            </div>
        <?php endif; ?>
        
        <div class="dashboard-content">
            <div class="user-info">
                <h2>Mi Información</h2>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($usuario['username'] ?? 'N/A'); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email'] ?? 'N/A'); ?></p>
            </div>
            
            <div class="tickets-section">
                <h2>Mis Tickets</h2>
                <?php if (isset($tickets) && count($tickets) > 0): ?>
                    <div class="tickets-grid">
                        <?php foreach ($tickets as $ticket): ?>
                            <div class="ticket-card">
                                <h3>Ticket #<?php echo htmlspecialchars($ticket['idTicket']); ?></h3>
                                <p><strong>Viaje:</strong> <?php echo htmlspecialchars($ticket['ciudadOrigen'] ?? 'N/A'); ?> - <?php echo htmlspecialchars($ticket['ciudadFinal'] ?? 'N/A'); ?></p>
                                <p><strong>Fecha:</strong> <?php echo htmlspecialchars($ticket['fecha'] ?? 'N/A'); ?></p>
                                <p><strong>Hora:</strong> <?php echo htmlspecialchars($ticket['hInicio'] ?? 'N/A'); ?></p>
                                <p><strong>Asiento:</strong> <?php echo htmlspecialchars($ticket['numeroAsiento'] ?? 'N/A'); ?></p>
                                <p><strong>Estado:</strong> <?php echo htmlspecialchars($ticket['estadoViaje'] ?? 'N/A'); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No tienes tickets aún. <a href="?action=comprarTicket">Compra tu primer ticket</a></p>
                <?php endif; ?>
            </div>
            
            <?php if (isset($estadisticas)): ?>
                <div class="statistics-section">
                    <h2>Estadísticas</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3><?php echo htmlspecialchars($estadisticas['totalViajes'] ?? '0'); ?></h3>
                            <p>Viajes Realizados</p>
                        </div>
                        <div class="stat-card">
                            <h3>$<?php echo htmlspecialchars($estadisticas['totalGastado'] ?? '0'); ?></h3>
                            <p>Total Gastado</p>
                        </div>
                        <div class="stat-card">
                            <h3><?php echo htmlspecialchars($estadisticas['viajesPendientes'] ?? '0'); ?></h3>
                            <p>Viajes Pendientes</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
