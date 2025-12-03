<?php
ob_start();
session_start();
include 'conexion_db.php';
include 'admin_header.php';
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: user_login.php");
    exit();
}

$id_cita = '';
$paciente_id_actual = ''; 
$medico_id_actual = '';   
$fecha = date('Y-m-d');
$hora = date('H:i');
$motivo = '';
$titulo = "Nueva Cita";

// CARGAR DATOS SI ES EDICIÓN
if (isset($_GET['id'])) {
    $id_cita = $_GET['id'];
    $titulo = "Editar Cita #$id_cita";
    $res = mysqli_query($conexion, "SELECT * FROM cita WHERE id_cita = '$id_cita'");
    $data = mysqli_fetch_assoc($res);
    
    $paciente_id_actual = $data['paciente_id'];
    $medico_id_actual = $data['medico_id'];
    $fecha = $data['fecha'];
    $hora = $data['hora'];
    $motivo = $data['motivo'];
}

// GUARDAR DATOS
if (isset($_POST['guardar'])) {
    $p = $_POST['paciente_id']; 
    $m = $_POST['medico_id'];
    $f = $_POST['fecha'];
    $h = $_POST['hora'];
    $mot = mysqli_real_escape_string($conexion, $_POST['motivo']);

    // LOGICA AUTOMÁTICA: Obtener especialidad del médico seleccionado
    $query_esp = mysqli_query($conexion, "SELECT especialidad_id FROM medico WHERE id_medico = '$m'");
    $row_esp = mysqli_fetch_assoc($query_esp);
    $esp_id = $row_esp['especialidad_id']; // ID de especialidad automática

    if ($id_cita) {
        // UPDATE
        $sql = "UPDATE cita SET paciente_id='$p', medico_id='$m', especialidad_id='$esp_id', fecha='$f', hora='$h', motivo='$mot' WHERE id_cita='$id_cita'";
    } else {
        // INSERT
        $sql = "INSERT INTO cita (paciente_id, medico_id, especialidad_id, fecha, hora, motivo) VALUES ('$p', '$m', '$esp_id', '$f', '$h', '$mot')";
    }

    if(mysqli_query($conexion, $sql)){
        header("Location: admin_citas.php"); 
        exit;
    } else {
        echo "Error: " . mysqli_error($conexion);
    }
}

// Consultas para los desplegables
$lista_pacientes = mysqli_query($conexion, "SELECT id_paciente, nombre, apellidos FROM paciente ORDER BY apellidos");
$lista_medicos   = mysqli_query($conexion, "SELECT m.id_medico, m.nombre, m.apellidos, e.nombre as esp_nom FROM medico m JOIN especialidad e ON m.especialidad_id = e.id_especialidad ORDER BY apellidos");
?>

<h2><?php echo $titulo; ?></h2>

<form method="POST">
    
    <label>Paciente:</label><br>
    <select name="paciente_id" required>
        <option value="">-- Seleccione --</option>
        <?php while($pac = mysqli_fetch_assoc($lista_pacientes)) { 
            $sel = ($pac['id_paciente'] == $paciente_id_actual) ? 'selected' : ''; ?>
            <option value="<?php echo $pac['id_paciente']; ?>" <?php echo $sel; ?>>
                <?php echo $pac['apellidos'] . ", " . $pac['nombre']; ?>
            </option>
        <?php } ?>
    </select>

    <label>Médico (La especialidad se asignará auto.):</label><br>
    <select name="medico_id" required>
        <option value="">-- Seleccione --</option>
        <?php while($med = mysqli_fetch_assoc($lista_medicos)) { 
            $sel = ($med['id_medico'] == $medico_id_actual) ? 'selected' : ''; ?>
            <option value="<?php echo $med['id_medico']; ?>" <?php echo $sel; ?>>
                Dr. <?php echo $med['apellidos']; ?> (<?php echo $med['esp_nom']; ?>)
            </option>
        <?php } ?>
    </select>
    
    <label>Fecha:</label><br>
    <input type="date" name="fecha" value="<?php echo $fecha; ?>" required>
    
    <label>Hora:</label><br>
    <input type="time" name="hora" value="<?php echo $hora; ?>" required>
    
    <label>Motivo de Consulta:</label><br>
    <textarea name="motivo" rows="3"><?php echo $motivo; ?></textarea>
    
    <input type="submit" name="guardar" value="Guardar Cita">
    <a href="admin_citas.php">Cancelar</a>
</form>
