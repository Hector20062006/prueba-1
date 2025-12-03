<?php
session_start();
include("conexion_db.php");

// Solo accesible para administradores
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: user_login.php");
    exit();
}

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