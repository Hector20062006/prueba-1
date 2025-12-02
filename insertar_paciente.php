<?php
session_start();
include("conexion_db.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: user_login.php");
    exit();
}
$dni = $_POST['dni'];
$password_hash = $_POST['password'];
$rol = $_POST['rol'];
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];
$direccion = $_POST['direccion'];
$afecciones = $_POST['afecciones'];

// Insertar en la tabla paciente
$query_paciente = "INSERT INTO paciente (nombre, apellidos, fecha_nacimiento, email, telefono, direccion, afecciones)
                VALUES ('$nombre', '$apellidos', '$fecha_nacimiento', '$email', '$telefono', '$direccion', '$afecciones')";

mysqli_query($conexion, $query_paciente);

$paciente_id = mysqli_insert_id($conexion);

// Insertar en la tabla usuario
$query_usuario = "INSERT INTO usuario (DNI, password_hash, rol, paciente_id)
                VALUES ('$dni', '$password_hash', '$rol', $paciente_id)";

mysqli_query($conexion, $query_usuario);

// Limpiar la sesión y redirigir al admin
unset($_SESSION['pending_new_user']);
header("Location: admin.php");

exit();
?>