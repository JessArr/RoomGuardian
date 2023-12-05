<!DOCTYPE html>
<html>
<head>
    <title>Registro Confirmado - RoomGuardian</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4; /* Color de fondo opcional */
        }

        .confirmation-card {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            border: 2px solid #3498db; /* Color del borde */
            border-radius: 10px;
            background-color: #fff; /* Color de fondo de la tarjeta */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra opcional */
        }

        .confirmation-card h1 {
            color: #3498db; /* Color del encabezado */
        }

        .confirmation-card p {
            color: #333; /* Color del texto */
        }
    </style>
</head>
<body>
<div class="confirmation-card">
    <h1>¡Registro Confirmado en RoomGuardian!</h1>
    <p>Hola {{ $user->name }}, tu correo electrónico ({{ $user->email }}) ha sido verificado. ¡Bienvenido a RoomGuardian!</p>
</div>
</body>
</html>
