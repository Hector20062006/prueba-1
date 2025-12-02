<?php
include("header.php");
include("conexion_db.php");
?>
<?php
session_start();

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

<h2>Registro de Médico</h2>
<p>Necesitamos unos cuantos mas datos de el medico</p>

<form action="insertar_medico.php" method="POST">

    <!-- DATOS DEL USUARIO (OCULTOS) -->
    <input type="hidden" name="dni" value="<?php echo $dni; ?>">
    <input type="hidden" name="password" value="<?php echo $password_hash; ?>">
    <input type="hidden" name="rol" value="<?php echo $rol; ?>">
    <br>

    <!-- CAMPOS PROPIOS DE MÉDICO -->
    <label>Nombre:</label>
    <input type="text" name="nombre" required><br><br>

    <label>Apellidos:</label>
    <input type="text" name="apellidos" required><br><br>

    <label for="especialidad">Especialidad médica:</label>
    <?php
        $query = "SELECT id_especialidad, nombre FROM especialidad ORDER BY nombre ASC";
        $result = mysqli_query($conexion, $query);
    ?>
    <select id="especialidad" name="especialidad" class="input-select especialidades">
    <option value="">-- Selecciona una especialidad --</option>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <option value="<?php echo $row['id_especialidad']; ?>"> <?php echo $row['nombre']; ?> </option>
    <?php } ?>
    </select><br><br>

    <label>Email:</label>
    <input type="text" name="email" required><br><br>

    <label>Telefono:</label>
    <input type="text" name="telefono" required><br><br>

    <input type="submit" name="crear_medico" value="Crear Médico">

</form>

<?php
include("footer.php");
?>