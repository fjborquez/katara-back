<!DOCTYPE html>
<html>
    <title>Invitación</title>
    <body>
        <h1>Hola {{ $person['name'] }} {{ $person['lastname'] }},</h1>
        <p>Nos complace invitarte a nuestro nuevo sitio. Para entrar, debes actualizar tu contraseña siguiendo este <a href="{{ $korraUrl }}/reset-password?token={{ $token }}">enlace</a>.  </p>
        <p>Muchas gracias por ser un miembro valioso de nuestra comunidad.</p>
        <p>Atentamente,<br>Avatar Team</p>
</html>
