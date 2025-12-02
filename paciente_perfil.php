<?php
session_start();
include("conexion_db.php");
include("paciente_header.php"); 

// Verificar seguridad (igual que en tus otros archivos)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'paciente') {
    header("Location: user_login.php");
    exit();
}

$id_paciente = $_SESSION['paciente_id'];
$mensaje = "";

// 1. ACTUALIZAR DATOS (Lógica igual a admin_logic.php)
if (isset($_POST['actualizar'])) {
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $afecciones = $_POST['afecciones'];

    // Actualizar tabla paciente
    $query_update = "UPDATE paciente 
                     SET email='$email', telefono='$telefono', direccion='$direccion', afecciones='$afecciones' 
                     WHERE id_paciente='$id_paciente'";
    
    mysqli_query($conexion, $query_update);
    $mensaje = "Datos actualizados.";

    // Actualizar contraseña SOLO si el campo no está vacío
    if (!empty($_POST['password'])) {
        $password_hash = hash('sha256', $_POST['password']);
        $query_pass = "UPDATE usuario 
                       SET password_hash='$password_hash' 
                       WHERE paciente_id='$id_paciente'";
        mysqli_query($conexion, $query_pass);
        $mensaje = "Datos y contraseña actualizados.";
    }
}

// 2. OBTENER DATOS ACTUALES (Para rellenar el formulario)
$query = "SELECT * FROM paciente WHERE id_paciente = '$id_paciente'";
$result = mysqli_query($conexion, $query);
$datos = mysqli_fetch_assoc($result);
?>

<h2>Mis Datos Personales</h2>

<?php if($mensaje != "") { echo "<p><strong>$mensaje</strong></p>"; } ?>

<form method="POST" action="">
    
    <p><strong>Paciente:</strong> <?php echo $datos['nombre'] . " " . $datos['apellidos']; ?></p>
    <br>

    <label>Email:</label>
    <input type="email" name="email" value="<?php echo $datos['email']; ?>" required><br><br>

    <label>Teléfono:</label>
    <input type="text" name="telefono" value="<?php echo $datos['telefono']; ?>" required><br><br>

    <label>Dirección:</label>
    <input type="text" name="direccion" value="<?php echo $datos['direccion']; ?>"><br><br>

    <label>Afecciones / Alergias:</label><br>
    <textarea name="afecciones" rows="4"><?php echo $datos['afecciones']; ?></textarea><br><br>

    <hr>
    
    <label>Nueva Contraseña (Opcional):</label>
    <input type="password" name="password" placeholder="Dejar en blanco para no cambiar"><br><br>

    <input type="submit" name="actualizar" value="Guardar Cambios">

</form>

<?php include("footer.php"); ?>