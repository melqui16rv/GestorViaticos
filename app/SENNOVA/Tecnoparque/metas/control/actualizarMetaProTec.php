<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/tecnoparque/metas.php';
requireRole(['5']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/@tailwindcss/browser@latest"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        .form-label {
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-input, .form-select {
            border-radius: 0.375rem;
            border-width: 1px;
            border-color: #d1d5db;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            line-height: 1.5rem;
            width: 100%;
            transition: border-color 0.15s ease-in-out, shadow-sm 0.15s ease-in-out;
            outline: none;
            background-color: white;
        }

        .form-input:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .form-input.error {
            border-color: #dc2626;
        }

        .form-input.success {
            border-color: #16a34a;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .password-container {
            position: relative;
            display: flex;
            width: 100%;
        }

        .password-input {
            width: 100%;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
            z-index: 10;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: #3b82f6;
        }

        .form-submit {
            background-color: #4CAF50;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            margin-top: 2rem;
            width: 100%;
            max-width: 320px;
            align-self: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-submit:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .form-submit:active {
            background-color: #388E3C;
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 800px;
        }

        .form-section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 1rem;
        }

        .password-strength {
            margin-top: 0.5rem;
            height: 0.5rem;
            border-radius: 0.375rem;
            background-color: #f3f4f6;
            position: relative;
            overflow: hidden;
        }

        .password-strength-bar {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            border-radius: 0.375rem;
            transition: width 0.3s ease;
        }

        .password-strength-text {
            font-size: 0.875rem;
            color: #4b5563;
            margin-top: 0.25rem;
            text-align: center;
        }

        .back-button {
            position: absolute;
            top: 1rem;
            left: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            background-color: #e5e7eb;
            color: #374151;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-button:hover {
            background-color: #d1d5db;
        }

        .back-button i {
            font-size: 1.25rem;
        }
        .password-toggle {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 1.5rem;
    z-index: 10;
    transition: transform 0.2s ease;
}

.password-toggle:hover {
    transform: translateY(-50%) scale(1.2);
}

.password-toggle:active {
    animation: bounce 0.3s;
}

@keyframes bounce {
    0%   { transform: translateY(-50%) scale(1); }
    50%  { transform: translateY(-50%) scale(1.3); }
    100% { transform: translateY(-50%) scale(1); }
}

    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen py-8">
    <button class="back-button" onclick="window.history.back()">
        <i class="fas fa-arrow-left"></i> Regresar
    </button>
    <div class="card">
        <form action="" method="POST" class="space-y-6" autocomplete="off">
           

            <input type="submit" value="Crear Usuario" name="Registrar" class="form-submit">
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
</body>
</html>
