<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Transporte</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Registro de Usuario</h2>
            
            <?php if (isset($errores) && count($errores) > 0): ?>
                <div class="error-message">
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="?action=registro">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" maxlength="50" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" maxlength="100" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" maxlength="10" required>
                    <small>Máximo 10 caracteres</small>
                </div>
                
                <div class="form-group">
                    <label for="confirmar_password">Confirmar Contraseña:</label>
                    <input type="password" id="confirmar_password" name="confirmar_password" maxlength="10" required>
                </div>
                
                <button type="submit">Registrarse</button>
            </form>
            
            <p>
                ¿Ya tiene cuenta? <a href="?action=login">Iniciar Sesión</a>
            </p>
        </div>
    </div>
</body>
</html>
