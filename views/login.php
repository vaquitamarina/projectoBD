<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Transporte</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Iniciar Sesión</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso'): ?>
                <div class="success-message">
                    Usuario registrado exitosamente. Puede iniciar sesión.
                </div>
            <?php endif; ?>
            
            <form method="POST" action="?action=login">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit">Iniciar Sesión</button>
            </form>
            
            <p>
                ¿No tiene cuenta? <a href="?action=registro">Registrarse</a>
            </p>
        </div>
    </div>
</body>
</html>
