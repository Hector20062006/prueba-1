<?php include("header.php"); ?>
<h2>Iniciar Sesión</h2>
<div class="formulario">
    <form action="user_login2.php" method="post">
        <label for="DNI">DNI:</label>
        <input type="text" id="DNI" name="DNI" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Iniciar Sesion</button>
        <p>No tienes cuenta?</p>
        <a href="register.php" class="boton-registro">Regístrate aquí</a>
    </form>
</div>
<?php include("footer.php"); ?>