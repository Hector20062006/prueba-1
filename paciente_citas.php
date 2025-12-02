<?php
session_start();
include("conexion_db.php");
include("paciente_header.php");

// 1. SEGURIDAD
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'paciente') {
    header("Location: user_login.php");
    exit();
}

// 2. OBTENER ID
$id_paciente = $_SESSION['paciente_id']; 

// 3. PROCESAR CITA (INSERT)
$mensaje = "";
if (isset($_POST['pedir_cita'])) {
    $medico_id = $_POST['medico_id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $motivo = mysqli_real_escape_string($conexion, $_POST['motivo']);

    // Buscar especialidad del médico
    $consulta_esp = mysqli_query($conexion, "SELECT especialidad_id FROM medico WHERE id_medico = '$medico_id'");
    $dato_esp = mysqli_fetch_assoc($consulta_esp);
    $especialidad_id = $dato_esp['especialidad_id'];

    $sql = "INSERT INTO cita (paciente_id, medico_id, especialidad_id, fecha, hora, motivo) 
            VALUES ('$id_paciente', '$medico_id', '$especialidad_id', '$fecha', '$hora', '$motivo')";
    
    if(mysqli_query($conexion, $sql)){
        header("Location: paciente_citas.php");
        exit;
    } else {
        $mensaje = "Error al guardar: " . mysqli_error($conexion);
    }
}

// 4. PROCESAR CANCELACIÓN (DELETE)
if (isset($_GET['cancelar_id'])) {
    $id_cancelar = $_GET['cancelar_id'];
    
    // Verificar propiedad de la cita
    $check_sql = "SELECT id_cita FROM cita WHERE id_cita = '$id_cancelar' AND paciente_id = '$id_paciente'";
    $check_res = mysqli_query($conexion, $check_sql);

    if (mysqli_num_rows($check_res) > 0) {
        $sql_borrar = "DELETE FROM cita WHERE id_cita = '$id_cancelar'";
        if(mysqli_query($conexion, $sql_borrar)){
            header("Location: paciente_citas.php");
            exit;
        } else {
            echo "Error al cancelar.";
        }
    } else {
        echo "No puedes cancelar esta cita.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Portal del Paciente</title>
</head>
<body>

    <div>
        <h2>Bienvenido al Portal del Paciente</h2>
    </div>

    <hr>

    <div>
        <h3>Solicitar Nueva Cita</h3>
        
        <?php if($mensaje != "") echo "<p>$mensaje</p>"; ?>

        <form method="POST">
            <label>Seleccione Especialista:</label><br>
            <select name="medico_id" required>
                <option value="">-- Seleccione Doctor --</option>
                <?php
                // Agrupación por especialidad usando OPTGROUP
                $q_medicos = "SELECT m.id_medico, m.nombre, m.apellidos, e.nombre as nombre_especialidad 
                              FROM medico m 
                              JOIN especialidad e ON m.especialidad_id = e.id_especialidad 
                              ORDER BY e.nombre ASC, m.apellidos ASC";
                $res_medicos = mysqli_query($conexion, $q_medicos);
                $grupo_actual = ""; 

                while($med = mysqli_fetch_assoc($res_medicos)) {
                    if ($grupo_actual != $med['nombre_especialidad']) {
                        if ($grupo_actual != "") { echo "</optgroup>"; } 
                        echo "<optgroup label='" . $med['nombre_especialidad'] . "'>"; 
                        $grupo_actual = $med['nombre_especialidad'];
                    }
                    echo "<option value='".$med['id_medico']."'>Dr. ".$med['apellidos']." ".$med['nombre']."</option>";
                }
                if ($grupo_actual != "") { echo "</optgroup>"; } 
                ?>
            </select>
            <br><br>
            
            <label>Fecha:</label><br>
            <input type="date" name="fecha" min="<?php echo date('Y-m-d'); ?>" required>
            <br><br>

            <label>Hora:</label><br>
            <input type="time" name="hora" required>
            <br><br>
            
            <label>Motivo:</label><br>
            <textarea name="motivo" required></textarea>
            <br><br>
            
            <input type="submit" name="pedir_cita" value="Confirmar Cita">
        </form>
    </div>

    <hr>

    <h3>Mis Citas Programadas</h3>
    
    <table border="1">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Médico</th>
                <th>Especialidad</th>
                <th>Motivo</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q_citas = "SELECT c.*, m.nombre as m_nom, m.apellidos as m_ape, e.nombre as esp_nom
                        FROM cita c
                        JOIN medico m ON c.medico_id = m.id_medico
                        JOIN especialidad e ON c.especialidad_id = e.id_especialidad
                        WHERE c.paciente_id = '$id_paciente'
                        ORDER BY c.fecha DESC";
            
            $res_citas = mysqli_query($conexion, $q_citas);

            if (mysqli_num_rows($res_citas) == 0) {
                echo "<tr><td colspan='7'>No tiene citas registradas.</td></tr>";
            }

            while($fila = mysqli_fetch_assoc($res_citas)) {
                $fecha_cita = date("d/m/Y", strtotime($fila['fecha']));
                $es_pasada = ($fila['fecha'] < date('Y-m-d'));
                $estado_texto = $es_pasada ? "Finalizada" : "Pendiente";
            ?>
            <tr>
                <td><?php echo $fecha_cita; ?></td>
                <td><?php echo $fila['hora']; ?></td>
                <td>Dr. <?php echo $fila['m_ape']." ".$fila['m_nom']; ?></td>
                <td><?php echo $fila['esp_nom']; ?></td>
                <td><?php echo $fila['motivo']; ?></td>
                <td><?php echo $estado_texto; ?></td>
                <td>
                    <?php if (!$es_pasada): ?>
                        <a href="paciente_citas.php?cancelar_id=<?php echo $fila['id_cita']; ?>" 
                           onclick="return confirm('¿Cancelar cita?');">
                           Cancelar
                        </a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
<?php include("footer.php"); ?>