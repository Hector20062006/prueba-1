<?php
ob_start();
include("admin_auth.php");
include("admin_header.php");
?>
<?php
include("conexion_db.php");
/* ============================================================
   CREAR USUARIO NUEVO
   ============================================================ */
if (isset($_POST['crear'])) {
    $dni = mysqli_real_escape_string($conexion, $_POST['dni']);
    $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $rol = $_POST['rol'];

    // Guardar sesión temporal si es médico o paciente
    if ($rol == 'medico' || $rol == 'paciente') {

        $_SESSION['pending_new_user'] = [
            'dni' => $dni,
            'password' => $_POST['password'],
            'rol' => $rol
        ];

        if ($rol == 'medico') {
            header('Location: admin_crear_medico.php');
            exit();
        } else {
            header('Location: admin_crear_paciente.php');
            exit();
        }
    }

    // Si es admin, se inserta directamente
    $estado = 'activo';

    $query = "INSERT INTO usuario (DNI, password_hash, rol, estado)
              VALUES ('$dni', '$password_hash', '$rol', '$estado')";

    if (!mysqli_query($conexion, $query)) {
        echo "Error al crear usuario: " . mysqli_error($conexion);
    }
}

/* ============================================================
   BORRAR USUARIO
   ============================================================ */
if (isset($_GET['borrar'])) {
    $id = intval($_GET['borrar']);

    if ($id != $_SESSION['usuario_id']) {
        mysqli_query($conexion, "DELETE FROM usuario WHERE id_usuario=$id");
    }

    header("Location: admin.php");
    exit();
}

/* ============================================================
   ACTUALIZAR USUARIO
   ============================================================ */
if (isset($_POST['actualizar'])) {
    $id = intval($_POST['id_usuario']);
    $dni = mysqli_real_escape_string($conexion, $_POST['dni']);
    $rol = $_POST['rol'];
    $estado = $_POST['estado'];

    $medico_id = !empty($_POST['medico_id']) ? intval($_POST['medico_id']) : "NULL";
    $paciente_id = !empty($_POST['paciente_id']) ? intval($_POST['paciente_id']) : "NULL";

    // Si cambia contraseña
    if (!empty($_POST['password'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $query = "
            UPDATE usuario SET 
                DNI='$dni', 
                password_hash='$password_hash',
                rol='$rol',
                medico_id=$medico_id,
                paciente_id=$paciente_id,
                estado='$estado'
            WHERE id_usuario=$id
        ";
    } else {
        // Sin contraseña
        $query = "
            UPDATE usuario SET 
                DNI='$dni', 
                rol='$rol',
                medico_id=$medico_id,
                paciente_id=$paciente_id,
                estado='$estado'
            WHERE id_usuario=$id
        ";
    }

    if (!mysqli_query($conexion, $query)) {
        echo "Error al actualizar: " . mysqli_error($conexion);
    } else {
        header("Location: admin.php");
    }
}

/* ============================================================
   BÚSQUEDA
   ============================================================ */
$busqueda = "";
if (isset($_POST['buscar']) && !empty($_POST['termino_busqueda'])) {
    $busqueda = mysqli_real_escape_string($conexion, $_POST['termino_busqueda']);

    $query = "
        SELECT * FROM usuario
        WHERE DNI LIKE '%$busqueda%'
        OR rol LIKE '%$busqueda%'
        OR CAST(medico_id AS CHAR) LIKE '%$busqueda%'
        ORDER BY id_usuario
    ";
    $result = mysqli_query($conexion, $query);
} else {
    $result = mysqli_query($conexion, "SELECT * FROM usuario ORDER BY id_usuario");
}

/* ============================================================
   CREAR ESPECIALIDAD
   ============================================================ */
if (isset($_POST['crear_especialidad'])) {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre_especialidad']);
    mysqli_query($conexion, "INSERT INTO especialidad (nombre) VALUES ('$nombre')");
}
?>

<h1>Panel de Administración de Usuarios</h1>

<!-- Crear usuario -->
<form action="admin.php" method="post">
    <h3>Crear nuevo usuario</h3>
    <input type="text" name="dni" placeholder="DNI" required>
    <input type="password" name="password" placeholder="Contraseña" required>

    <select name="rol">
        <option value="medico">Médico</option>
        <option value="admin">Admin</option>
        <option value="paciente">Paciente</option>
    </select>

    <input type="submit" name="crear" value="Crear usuario">
</form>

<hr>

<!-- Búsqueda -->
<form method="POST">
    <h3>Buscar usuario</h3>
    <input type="text" name="termino_busqueda" 
           value="<?php echo htmlspecialchars($busqueda); ?>" 
           placeholder="DNI, Rol o Medico ID">
    <input type="submit" name="buscar" value="Buscar">
    <input type="submit" name="mostrar_todos" value="Mostrar todos">
</form>

<br><hr><br>

<!-- Tabla -->
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>DNI</th>
        <th>Rol</th>
        <th>Médico ID</th>
        <th>Paciente ID</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>

<?php while ($fila = mysqli_fetch_assoc($result)) { ?>
<tr>
<form method="POST">
    <td>
        <input type="hidden" name="id_usuario" value="<?php echo $fila['id_usuario']; ?>">

        <?php if ($fila['rol'] == 'medico' || $fila['rol'] == 'paciente') { ?>
            <a href="admin_usuario_info.php?id=<?php echo $fila['id_usuario']; ?>">
                <?php echo $fila['id_usuario']; ?>
            </a>
        <?php } else {
            echo $fila['id_usuario'];
        } ?>
    </td>

    <td><input type="text" name="dni" value="<?php echo htmlspecialchars($fila['DNI']); ?>"></td>

    <td>
        <select name="rol">
            <option value="admin"    <?php if($fila['rol']=='admin') echo 'selected'; ?>>Admin</option>
            <option value="medico"   <?php if($fila['rol']=='medico') echo 'selected'; ?>>Médico</option>
            <option value="paciente" <?php if($fila['rol']=='paciente') echo 'selected'; ?>>Paciente</option>
        </select>
    </td>

    <td><input type="number" name="medico_id" value="<?php echo $fila['medico_id']; ?>"></td>
    <td><input type="number" name="paciente_id" value="<?php echo $fila['paciente_id']; ?>"></td>

    <td>
        <select name="estado">
            <option value="activo"    <?php if($fila['estado']=='activo') echo 'selected'; ?>>Activo</option>
            <option value="pendiente" <?php if($fila['estado']=='pendiente') echo 'selected'; ?>>Pendiente</option>
            <option value="inactivo"  <?php if($fila['estado']=='inactivo') echo 'selected'; ?>>Deshabilitado</option>
        </select>
    </td>

    <td>
        <input type="password" name="password" placeholder="Nueva contraseña (opcional)">
        <input type="submit" name="actualizar" value="Actualizar">
        <a href="admin.php?borrar=<?php echo $fila['id_usuario']; ?>" 
           onclick="return confirm('¿Seguro que deseas borrar este usuario?');">
           <img src="imgs/papelera.png" height="20">
        </a>
    </td>
</form>
</tr>
<?php } ?>
</table>

<?php include("footer.php"); ?>