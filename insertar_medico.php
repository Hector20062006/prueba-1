<?php
include("conexion_db.php");

session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: user_login.php");
    exit();
}


$dni = $_POST['dni'];
$password_hash = $_POST['password'];
$rol = $_POST['rol'];
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$especialidad = $_POST['especialidad'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];

// Insertar en la tabla medico
$query_medico = "INSERT INTO medico (nombre, apellidos,especialidad_id, email, telefono)
                VALUES ('$nombre', '$apellidos', '$especialidad', '$email', '$telefono')";
mysqli_query($conexion, $query_medico);
$medico_id = mysqli_insert_id($conexion);

// Insertar en la tabla usuario
$query_usuario = "INSERT INTO usuario (DNI, password_hash, rol, medico_id)
                VALUES ('$dni', '$password_hash', '$rol', $medico_id)";
mysqli_query($conexion, $query_usuario);

// Limpiar la sesión y redirigir al admin
unset($_SESSION['pending_new_user']);
header("Location: admin.php");
exit();


?>