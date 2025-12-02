<?php
// Asegúrate de iniciar sesión si no está en conexion_db.php
session_start();
include("header.php");

include("conexion_db.php");

// Verificación de seguridad (Admin)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: user_login.php");
    exit();
}

// Verificación del ID
if (!isset($_GET['id'])) {
    echo "No se ha especificado un ID.";
    exit;
}

$id_usuario = intval($_GET['id']);

// PASO 1: Averiguar el ROL del usuario primero
$query_rol = "SELECT rol FROM usuario WHERE id_usuario = $id_usuario";
$result_rol = mysqli_query($conexion, $query_rol);

if (!$result_rol || mysqli_num_rows($result_rol) == 0) {
    echo "Usuario no encontrado.";
    exit;
}

$data_usuario = mysqli_fetch_assoc($result_rol);
$rol = $data_usuario['rol'];
$datos = null; // Aquí guardaremos la info final

// PASO 2: Consulta dinámica según el rol
if ($rol == 'medico') {
    // Consulta para MÉDICO (con Especialidad)
    $query = "
    SELECT u.id_usuario, u.DNI, m.nombre, m.apellidos, m.email, m.telefono, e.nombre AS dato_extra
    FROM usuario u
    INNER JOIN medico m ON u.medico_id = m.id_medico
    INNER JOIN especialidad e ON m.especialidad_id = e.id_especialidad
    WHERE u.id_usuario = $id_usuario";
    
    $titulo_extra = "Especialidad"; // Etiqueta para el campo variable

} elseif ($rol == 'paciente') {
    // Consulta para PACIENTE (con Dirección, etc.)
    // Nota: concatenamos dirección para el ejemplo, o puedes sacar más campos
    $query = "
    SELECT u.id_usuario, u.DNI, p.nombre, p.apellidos, p.email, p.telefono, p.direccion AS dato_extra, p.fecha_nacimiento, p.afecciones
    FROM usuario u
    INNER JOIN paciente p ON u.paciente_id = p.id_paciente
    WHERE u.id_usuario = $id_usuario";
    
    $titulo_extra = "Dirección"; // Etiqueta para el campo variable

} else {
    echo "El rol de este usuario ('$rol') no tiene ficha de detalles configurada.";
    exit;
}

// Ejecutar la consulta específica
$result = mysqli_query($conexion, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Error: El usuario existe pero no tiene sus datos enlazados en la tabla de $rol.";
    exit;
}

$ficha = mysqli_fetch_assoc($result);
?>

<div class="ficha-usuario">
    <h2>Información del <?php echo ucfirst($rol); // Pone la primera letra en mayúscula ?></h2>
    
    <p><strong>ID Usuario:</strong> <?php echo $ficha['id_usuario']; ?></p>
    <p><strong>DNI:</strong> <?php echo $ficha['DNI']; ?></p>
    <p><strong>Nombre:</strong> <?php echo $ficha['nombre']; ?></p>
    <p><strong>Apellidos:</strong> <?php echo $ficha['apellidos']; ?></p>
    <p><strong>Email:</strong> <?php echo $ficha['email']; ?></p>
    <p><strong>Teléfono:</strong> <?php echo $ficha['telefono']; ?></p>
    
    <p><strong><?php echo $titulo_extra; ?>:</strong> <?php echo $ficha['dato_extra']; ?></p>

    <?php if ($rol == 'paciente'): ?>
        <p><strong>Fecha Nacimiento:</strong> <?php echo $ficha['fecha_nacimiento']; ?></p>
        <p><strong>Afecciones:</strong> <?php echo $ficha['afecciones']; ?></p>
    <?php endif; ?>

    <br>
    <a href="admin.php">Volver al panel de administración</a>
</div>

<?php
include("footer.php");
?>