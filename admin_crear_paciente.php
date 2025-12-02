<?php
session_start();
include("header.php");
include("conexion_db.php");
?>
<?php

// Si no existe la sesión, volvemos al panel
if (!isset($_SESSION['pending_new_user'])) {
    header("Location: admin.php");
    exit();
}

$usuario = $_SESSION['pending_new_user'];
$dni = $usuario['dni'];
$rol = $usuario['rol'];
$password_hash = hash("sha256", $usuario['password']);
?>

<h2>Registro del Paciente</h2>
<p>Necesitamos unos cuantos mas datos de el Paciente</p>

<form action="insertar_paciente.php" method="POST">

    <!-- DATOS DEL USUARIO (OCULTOS) -->
    <input type="hidden" name="dni" value="<?php echo $dni; ?>">
    <input type="hidden" name="password" value="<?php echo $password_hash; ?>">
    <input type="hidden" name="rol" value="<?php echo $rol; ?>">

    <!-- CAMPOS PROPIOS DE PACIENTE -->
    <label>Nombre:</label>
    <input type="text" name="nombre" required><br><br>

    <label>Apellidos:</label>
    <input type="text" name="apellidos" required><br><br>

    <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required><br><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br><br>

    <label for="telefono">Teléfono:</label>
    <input type="tel" id="telefono" name="telefono" required><br><br>

    <label for="direccion">Dirección:</label>
    <input type="text" id="direccion" name="direccion" placeholder="Calle, Número, Ciudad"><br><br>

    <label for="afecciones">Afecciones / Alergias:</label><br>
    <textarea id="afecciones" name="afecciones" placeholder="Describa condiciones previas, alergias, etc."></textarea><br><br>

    <input type="submit" name="crear_paciente" value="Crear Paciente">

</form>

<?php
include("footer.php");
?>