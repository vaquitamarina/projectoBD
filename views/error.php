<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Sistema de Transportes</title>
    <script src="https://kit.fontawesome.com/f9a3e96628.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/crud.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1>Sistema de Transportes</h1>
            <nav>
                <a href="index.php">Inicio</a>
                <a href="?controlador=ticket">Tickets</a>
            </nav>
        </header>

        <main>
            <div class="error-container">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                
                <h2>¡Oops! Algo salió mal</h2>
                
                <div class="error-message">
                    <?php if (isset($error) && !empty($error)): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php else: ?>
                        <p>Ha ocurrido un error inesperado. Por favor, intenta nuevamente.</p>
                    <?php endif; ?>
                </div>
                
                <div class="error-actions">
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver atrás
                    </a>
                    <a href="?controlador=ticket" class="btn btn-primary">
                        <i class="fas fa-home"></i> Ir a Tickets
                    </a>
                    <a href="index.php" class="btn btn-info">
                        <i class="fas fa-home"></i> Ir al inicio
                    </a>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2025 Sistema de Transportes. Todos los derechos reservados.</p>
        </footer>
    </div>

    <style>
        .error-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 40px 0;
        }

        .error-icon {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .error-container h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 2em;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #f5c6cb;
            margin-bottom: 30px;
            max-width: 600px;
        }

        .error-message p {
            margin: 0;
            font-size: 1.1em;
        }

        .error-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        nav {
            margin-bottom: 20px;
        }

        nav a {
            margin-right: 15px;
            color: #007bff;
            text-decoration: none;
        }

        nav a:hover {
            text-decoration: underline;
        }

        footer {
            margin-top: 40px;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            border-top: 1px solid #eee;
        }

        @media (max-width: 768px) {
            .error-container {
                padding: 40px 15px;
            }
            
            .error-icon {
                font-size: 60px;
            }
            
            .error-container h2 {
                font-size: 1.5em;
            }
            
            .error-actions {
                flex-direction: column;
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</body>
</html>
