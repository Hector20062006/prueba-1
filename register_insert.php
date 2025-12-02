<?php
include("conexion_db.php");

$rol = $_POST['rol'];

// ==========================================
// LÓGICA PARA PACIENTE
// ==========================================
if ($rol == 'paciente') {
    
    // Recogida de datos directa
    $dni = $_POST['dni'];
    $password = hash('sha256', $_POST['password']); // Encriptación SHA256
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $fecha_nac = $_POST['fecha_nacimiento'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $afecciones = $_POST['afecciones'];
    
    // Fecha actual
    $fecha_registro = date("Y-m-d H:i:s");

    // 1. Insertar en tabla PACIENTE
    $query_paciente = "INSERT INTO paciente (nombre, apellidos, fecha_nacimiento, direccion, telefono, email, afecciones) 
                       VALUES ('$nombre', '$apellidos', '$fecha_nac', '$direccion', '$telefono', '$email', '$afecciones')";
    
    mysqli_query($conexion, $query_paciente);
    
    // Obtenemos el ID que se acaba de crear
    $paciente_id = mysqli_insert_id($conexion);

    // 2. Insertar en tabla USUARIO (Estado ACTIVO)
    $query_usuario = "INSERT INTO usuario (DNI, password_hash, rol, paciente_id, estado)
                      VALUES ('$dni', '$password', 'paciente', '$paciente_id', 'activo')";
    
    mysqli_query($conexion, $query_usuario);

    // Redirección
    echo "<script>alert('Registro completado. Ya puedes iniciar sesión.'); window.location.href='user_login.php';</script>";

// ==========================================
// LÓGICA PARA MÉDICO
// ==========================================
} elseif ($rol == 'medico') {

    // Recogida de datos directa
    $dni = $_POST['dni'];
    $password = hash('sha256', $_POST['password']); // Encriptación SHA256
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $especialidad = $_POST['especialidad'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    // 1. Insertar en tabla MEDICO
    $query_medico = "INSERT INTO medico (nombre, apellidos, especialidad_id, email, telefono)
                     VALUES ('$nombre', '$apellidos', '$especialidad', '$email', '$telefono')";
    
    mysqli_query($conexion, $query_medico);
    
    // Obtenemos el ID del médico
    $medico_id = mysqli_insert_id($conexion);

    // 2. Insertar en tabla USUARIO (Estado PENDIENTE)
    $query_usuario = "INSERT INTO usuario (DNI, password_hash, rol, medico_id, estado)
                      VALUES ('$dni', '$password', 'medico', '$medico_id', 'pendiente')";
    
    mysqli_query($conexion, $query_usuario);

    // Redirección
    echo "<script>alert('Registro recibido. Tu cuenta está PENDIENTE de validación por un administrador.'); window.location.href='user_login.php';</script>";
}
?>