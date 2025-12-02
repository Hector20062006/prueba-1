<?php
session_start();
include("conexion_db.php");
include("medico_header.php"); // Usamos el header específico de médicos

// 1. SEGURIDAD: Solo médicos
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'medico') {
    header("Location: user_login.php");
    exit();
}

$paciente = null;
$historial = null;
$mensaje_error = "";

// 2. LÓGICA DE BÚSQUEDA
if (isset($_POST['buscar'])) {
    $dni_busqueda = mysqli_real_escape_string($conexion, $_POST['dni']);

    // Buscar datos personales del paciente vinculando con la tabla usuario (donde está el DNI)
    $sql_paciente = "SELECT p.*, u.DNI 
                     FROM paciente p 
                     JOIN usuario u ON u.paciente_id = p.id_paciente 
                     WHERE u.DNI = '$dni_busqueda'";
    
    $res_paciente = mysqli_query($conexion, $sql_paciente);

    if (mysqli_num_rows($res_paciente) > 0) {
        $paciente = mysqli_fetch_assoc($res_paciente);
        $id_paciente = $paciente['id_paciente'];

        // 3. CONSULTA COMPLEJA DE HISTORIAL
        // Traemos: Cita, Médico que atendió, Diagnóstico, Observaciones y Medicamentos (agrupados)
        $sql_historial = "SELECT 
                            c.fecha, c.hora,
                            m.nombre as doc_nom, m.apellidos as doc_ape,
                            e.nombre as especialidad,
                            con.diagnostico, con.observaciones,
                            GROUP_CONCAT(r.medicamento SEPARATOR ', ') as medicamentos,
                            GROUP_CONCAT(r.dosis SEPARATOR ', ') as dosis
                          FROM cita c
                          LEFT JOIN medico m ON c.medico_id = m.id_medico
                          LEFT JOIN especialidad e ON c.especialidad_id = e.id_especialidad
                          LEFT JOIN consultas con ON c.id_cita = con.id_cita
                          LEFT JOIN recetas r ON con.id_consulta = r.consulta_id
                          WHERE c.paciente_id = '$id_paciente'
                          GROUP BY c.id_cita
                          ORDER BY c.fecha DESC";
        
        $historial = mysqli_query($conexion, $sql_historial);

    } else {
        $mensaje_error = "No se ha encontrado ningún paciente con el DNI: " . htmlspecialchars($dni_busqueda);
    }
}
?>

<h2>Consulta de Historial Clínico</h2>

<form method="POST" style="margin-bottom: 30px;">
    <h3>Buscar Paciente</h3>
    <label>Introduce DNI del Paciente:</label>
    <input type="text" name="dni" placeholder="Ej: 12345678A" required>
    <input type="submit" name="buscar" value="Buscar Historial">
    <?php if ($mensaje_error): ?>
        <p style="color: var(--danger); font-weight: bold; margin-top: 10px;"><?php echo $mensaje_error; ?></p>
    <?php endif; ?>
</form>

<?php if ($paciente): ?>

    <div class="ficha-usuario">
        <h2>Datos del Paciente</h2>
        
        <p><strong>Paciente:</strong> <?php echo $paciente['nombre'] . " " . $paciente['apellidos']; ?></p>
        <p><strong>DNI:</strong> <?php echo $paciente['DNI']; ?></p>
        <p><strong>Edad:</strong> 
            <?php 
                // Cálculo de edad automático
                $fecha_nac = new DateTime($paciente['fecha_nacimiento']);
                $hoy = new DateTime();
                $edad = $hoy->diff($fecha_nac);
                echo $edad->y . " años";
            ?>
        </p>
        <p><strong>Teléfono:</strong> <?php echo $paciente['telefono']; ?></p>
        
        <p style="background-color: #fff3cd; border-left: 5px solid #ffc107;">
            <strong>⚠ Afecciones / Alergias:</strong> 
            <?php echo $paciente['afecciones'] ? $paciente['afecciones'] : "Ninguna registrada"; ?>
        </p>
        
        <a href="#historial">Ver Historial Detallado ↓</a>
    </div>

    <hr id="historial">

    <h3>Historial de Consultas y Tratamientos</h3>
    
    <table border="1">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Especialista</th>
                <th>Diagnóstico y Observaciones</th>
                <th>Tratamiento Recetado</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($historial && mysqli_num_rows($historial) > 0) {
                while ($fila = mysqli_fetch_assoc($historial)) {
                    // Formatear fecha
                    $fecha_fmt = date("d/m/Y", strtotime($fila['fecha']));
                    
                    // Verificar si hay diagnóstico
                    $tiene_diagnostico = !empty($fila['diagnostico']);
            ?>
                <tr>
                    <td><?php echo $fecha_fmt; ?><br><small><?php echo $fila['hora']; ?></small></td>
                    
                    <td>
                        <strong>Dr. <?php echo $fila['doc_ape']; ?></strong><br>
                        <small>(<?php echo $fila['especialidad']; ?>)</small>
                    </td>
                    
                    <td>
                        <?php if ($tiene_diagnostico): ?>
                            <strong>Diagnóstico:</strong> <?php echo $fila['diagnostico']; ?><br>
                            <?php if($fila['observaciones']): ?>
                                <br><strong>Obs:</strong> <i><?php echo $fila['observaciones']; ?></i>
                            <?php endif; ?>
                        <?php else: ?>
                            <em style="color: #999;">Cita pendiente o sin informe cerrado.</em>
                        <?php endif; ?>
                    </td>
                    
                    <td>
                        <?php 
                        if ($fila['medicamentos']) {
                            // Separamos para mostrar mejor si hay varios
                            $meds = explode(", ", $fila['medicamentos']);
                            $dosis = explode(", ", $fila['dosis']);
                            
                            echo "<ul>";
                            for ($i = 0; $i < count($meds); $i++) {
                                echo "<li>" . $meds[$i];
                                if (isset($dosis[$i])) echo " (" . $dosis[$i] . ")";
                                echo "</li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "-";
                        }
                        ?>
                    </td>
                </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='4' style='text-align:center;'>Este paciente no tiene historial médico registrado en el sistema.</td></tr>";
            }
            ?>
        </tbody>
    </table>

<?php endif; ?>

<?php include("footer.php"); ?>