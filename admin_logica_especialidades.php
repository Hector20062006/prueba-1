<?php
include("conexion_db.php");

if (isset($_POST['actualizar_especialidad'])) {
    $id_esp = mysqli_real_escape_string($conexion, $_POST['id_especialidad']);
    $nombre_esp = mysqli_real_escape_string($conexion, $_POST['nombre']);
    
    $update_query = "UPDATE especialidad SET nombre = '$nombre_esp' WHERE id_especialidad = '$id_esp'";
    mysqli_query($conexion, $update_query);
}

if (isset($_GET['borrar_especialidad'])) {
    $id_borrar = mysqli_real_escape_string($conexion, $_GET['borrar_especialidad']);
    
    $delete_query = "DELETE FROM especialidad WHERE id_especialidad = '$id_borrar'";
    mysqli_query($conexion, $delete_query);
    header("Location: admin_especialidades.php");
    exit();
}

$especialidades_query = "SELECT id_especialidad, nombre FROM especialidad";
$especialidades_result = mysqli_query($conexion, $especialidades_query);
?>