<?php
include 'conexion_db.php'; 

// 1. GESTIÓN DE CITAS

// Actualizar Cita
if (isset($_POST['actualizar_cita'])) {
    $id = $_POST['id_cita'];
    $paciente = $_POST['paciente_id'];
    $medico = $_POST['medico_id'];
    $especialidad = $_POST['especialidad_id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $motivo = $_POST['motivo'];

    $sql = "UPDATE cita SET paciente_id='$paciente', medico_id='$medico', especialidad_id='$especialidad', fecha='$fecha', hora='$hora', motivo='$motivo' WHERE id_cita='$id'";
    mysqli_query($conexion, $sql);
}

// Borrar Cita (Ojo: Esto podría fallar si tiene consultas asociadas por las claves foráneas)
if (isset($_GET['borrar_cita'])) {
    $id = $_GET['borrar_cita'];
    mysqli_query($conexion, "DELETE FROM cita WHERE id_cita='$id'");
}else {
    echo "Error al borrar la cita: " . mysqli_error($conexion);
}

// 2. GESTIÓN DE CONSULTAS

// Actualizar Consulta
if (isset($_POST['actualizar_consulta'])) {
    $id = $_POST['id_consulta'];
    $diagnostico = $_POST['diagnostico'];
    $observaciones = $_POST['observaciones'];

    $sql = "UPDATE consultas SET diagnostico='$diagnostico', observaciones='$observaciones' WHERE id_consulta='$id'";
    mysqli_query($conexion, $sql);
}

// Borrar Consulta
if (isset($_GET['borrar_consulta'])) {
    $id = $_GET['borrar_consulta'];
    mysqli_query($conexion, "DELETE FROM consultas WHERE id_consulta='$id'");
}

// 3. GESTIÓN DE RECETAS

// Actualizar Receta
if (isset($_POST['actualizar_receta'])) {
    $id = $_POST['id_receta'];
    $medicamento = $_POST['medicamento'];
    $dosis = $_POST['dosis'];
    $instrucciones = $_POST['instrucciones'];

    $sql = "UPDATE recetas SET medicamento='$medicamento', dosis='$dosis', instrucciones='$instrucciones' WHERE id_receta='$id'";
    mysqli_query($conexion, $sql);
}

// Borrar Receta
if (isset($_GET['borrar_receta'])) {
    $id = $_GET['borrar_receta'];
    mysqli_query($conexion, "DELETE FROM recetas WHERE id_receta='$id'");
}

// CONSULTAS PARA LLENAR LAS TABLAS

// 1. Traer Citas (Ordenadas por fecha)
$q_citas = mysqli_query($conexion, "SELECT * FROM cita ORDER BY fecha DESC");

// 2. Traer Consultas (Unimos con cita para saber de quién es)
// Muestra ID Consulta, Fecha de la Cita y Paciente ID para dar contexto
$q_consultas = mysqli_query($conexion, "
    SELECT cons.*, c.fecha, c.paciente_id 
    FROM consultas cons 
    INNER JOIN cita c ON cons.id_cita = c.id_cita 
    ORDER BY c.fecha DESC
");

// 3. Traer Recetas (Solo si existen)
$q_recetas = mysqli_query($conexion, "SELECT * FROM recetas");
?>