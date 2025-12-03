<?php
session_start();
include("admin_header.php");
include("conexion_db.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: user_login.php");
    exit();
}
?>

<?php

// --- CREAR USUARIO NUEVO ---
if (isset($_POST['crear'])) {
    $dni = mysqli_real_escape_string($conexion, $_POST['dni']);
    // IMPORTANTE: Usamos password_hash para compatibilidad con login.php
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 
    $rol = $_POST['rol'];

    // Redirección si es médico o paciente (para llenar sus perfiles)
    if ($rol == 'medico' || $rol == 'paciente') {
        $_SESSION['pending_new_user'] = [
            'dni' => $dni,
            'password' => $_POST['password'], // Guardamos la plana para pasarla al siguiente form
            'rol' => $rol
        ];
        if ($rol == 'medico') {
            header('Location: admin_crear_medico.php');
            exit();
        }
        if ($rol == 'paciente') {
            header('Location: admin_crear_paciente.php');
            exit();
        }
    }

    // Si es ADMIN, lo creamos directamente
    // Definimos estado activo por defecto para admins creados aquí
    $estado_inicial = 'activo'; 
    
    $query = "INSERT INTO usuario (DNI, password_hash, rol, estado)
              VALUES ('$dni', '$password', '$rol', '$estado_inicial')";
    
    if (!mysqli_query($conexion, $query)) {
        echo "Error al crear: " . mysqli_error($conexion);
    }
}

// --- BORRAR USUARIO ---
if (isset($_GET['borrar'])) {
    $id = intval($_GET['borrar']);
    // Evitar borrar al propio admin conectado
    if ($id != $_SESSION['usuario_id']) {
        $query = "DELETE FROM usuario WHERE id_usuario = $id";
        mysqli_query($conexion, $query);
    }
}

// --- ACTUALIZAR USUARIO EXISTENTE ---
if (isset($_POST['actualizar'])) {
    $id = intval($_POST['id_usuario']);
    $dni = mysqli_real_escape_string($conexion, $_POST['dni']);
    $rol = $_POST['rol'];
    $estado = $_POST['estado']; // Capturamos el estado (activo/pendiente/inactivo)

    // --- Manejo de IDs sin operador ternario (IF / ELSE) ---
    
    // Para Médico ID
    if (!empty($_POST['medico_id'])) {
        $medico_id = $_POST['medico_id'];
    } else {
        $medico_id = "NULL";
    }

    // Para Paciente ID
    if (!empty($_POST['paciente_id'])) {
        $paciente_id = $_POST['paciente_id'];
    } else {
        $paciente_id = "NULL";
    }

    // --- Lógica de Actualización ---

    if (!empty($_POST['password'])) {
        // Si hay contraseña nueva, la encriptamos y actualizamos todo
        $password = password_hash($_POST['password'], 'sha256');
        
        $query = "UPDATE usuario 
                  SET DNI='$dni', password_hash='$password', rol='$rol', 
                      medico_id=$medico_id, paciente_id=$paciente_id, estado='$estado'
                  WHERE id_usuario=$id";
    } else {
        // Si NO hay contraseña nueva, actualizamos todo MENOS la contraseña
        $query = "UPDATE usuario 
                  SET DNI='$dni', rol='$rol', 
                      medico_id=$medico_id, paciente_id=$paciente_id, estado='$estado'
                  WHERE id_usuario=$id";
    }

    if (!mysqli_query($conexion, $query)) {
        echo "Error al actualizar: " . mysqli_error($conexion);
    } else {
        // Recargar página para ver cambios
        header("Location: admin.php");
        exit();
    }
}

// --- BÚSQUEDA Y LISTADO ---
$busqueda = "";
if (isset($_POST['buscar']) && !empty($_POST['termino_busqueda'])) {
    $busqueda = mysqli_real_escape_string($conexion, $_POST['termino_busqueda']);

    $query = "
        SELECT * FROM usuario
        WHERE DNI LIKE '%$busqueda%'
        OR rol LIKE '%$busqueda%'
        OR CAST(medico_id AS CHAR) LIKE '%$busqueda%'
        ORDER BY id_usuario ASC
    ";
    $result = mysqli_query($conexion, $query);
} else {
    $result = mysqli_query($conexion, "SELECT * FROM usuario ORDER BY id_usuario ASC");
}

// --- CREAR ESPECIALIDAD ---
if (isset($_POST['crear_especialidad'])) {
    $nombre_especialidad = mysqli_real_escape_string($conexion, $_POST['nombre_especialidad']);
    $query = "INSERT INTO especialidad (nombre) VALUES ('$nombre_especialidad')";
    mysqli_query($conexion, $query);
}
?>

<h1>Panel de Administración de Usuarios</h1>

<!-- Formulario para crear usuario -->
<form action="admin_logic.php" method="post">
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

<!-- Formulario de búsqueda -->
<form method="POST">
    <h3>Buscar usuario por DNI, Rol o ID Médico</h3>
    <input type="text" name="termino_busqueda" placeholder="Ejemplo: 12345678A o admin o 5" value="<?php echo htmlspecialchars($busqueda); ?>">
    <input type="submit" name="buscar" value="Buscar">
    <input type="submit" name="mostrar_todos" value="Mostrar todos">
</form>

<br>
<hr>
<br>
<!-- Tabla de usuarios -->
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
                <?php 
                if($fila['rol'] == 'medico' || $fila['rol'] == 'paciente' ) {
                    echo '<a href="admin_usuario_info.php?id=' . $fila['id_usuario'] . '">' . $fila['id_usuario'] . '</a>';
                } else {
                    echo $fila['id_usuario'];
                }
                ?>
            </td>
            <td><input type="text" name="dni" value="<?php echo $fila['DNI']; ?>"></td>
            <td>
                <select name="rol">
                    <option value="admin" <?php if($fila['rol'] == 'admin') echo 'selected'; ?>>Admin</option>
                    <option value="medico" <?php if($fila['rol'] == 'medico') echo 'selected'; ?>>Médico</option>
                    <option value="paciente" <?php if($fila['rol'] == 'paciente') echo 'selected'; ?>>Paciente</option>
                </select>
            </td>
            <td><input type="number" name="medico_id" value="<?php echo $fila['medico_id']; ?>"></td>
            <td><input type="number" name="paciente_id" value="<?php echo $fila['paciente_id']; ?>"></td>
            <td>
                <select name="estado">
                    <option value="activo" <?php if($fila['estado'] == 'activo') echo 'selected'; ?>>Activo</option>
                    <option value="pendiente" <?php if($fila['estado'] == 'pendiente') echo 'selected'; ?>>Pendiente</option>
                    <option value="inactivo" <?php if($fila['estado'] == 'inactivo') echo 'selected'; ?>>Deshabilitado</option>
                </select>
            <td>
                <input type="password" name="password" placeholder="Nueva contraseña (opcional)">
                <input type="submit" name="actualizar" value="Actualizar">
                <a href="admin_panel.php?borrar=<?php echo $fila['id_usuario']; ?>" 
                onclick="return confirm('¿Estás seguro de que quieres borrar este usuario?');">
                <img src="imgs/papelera.png" alt="Borrar" height="20"></a>
            </td>
        </form>
    </tr>
    <?php } ?>
</table>

<?php include("footer.php"); ?>

