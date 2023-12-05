<!DOCTYPE html>
<html>
<head>
    <title>Verificación de correo electrónico</title>
</head>
<body>
<p>Hola {{  $name }},</p>
<p>¿Cómo estás?</p>
<p>Por favor, haz clic en el siguiente botón para verificar tu correo electrónico:</p>
<a href="{{ $verificationUrl }}">
    <button>Verificar Email</button>
</a>
</body>
</html>
