<?php
session_start();
include("medico_header.php");
include("conexion_db.php");

// 1. SEGURIDAD: Solo Médicos
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'medico') {
    header("Location: user_login.php");
    exit();
}

// 2. OBTENER ID DEL MÉDICO
$id_mi_medico = $_SESSION['medico_id'];

// 3. LÓGICA DE BORRADO (Cancelar Cita)
if (isset($_GET['borrar_cita'])) {
    $id_borrar = mysqli_real_escape_string($conexion, $_GET['borrar_cita']);
    
    // Verificar que la cita pertenece a este médico
    $check = mysqli_query($conexion, "SELECT id_cita FROM cita WHERE id_cita='$id_borrar' AND medico_id='$id_mi_medico'");
    
    if (mysqli_num_rows($check) > 0) {
        // Borrar dependencias (Consultas y Recetas asociadas)
        $busca_cons = mysqli_query($conexion, "SELECT id_consulta FROM consultas WHERE id_cita = '$id_borrar'");
        if($cons = mysqli_fetch_assoc($busca_cons)){
            $id_c = $cons['id_consulta'];
            mysqli_query($conexion, "DELETE FROM recetas WHERE consulta_id = '$id_c'");
            mysqli_query($conexion, "DELETE FROM consultas WHERE id_consulta = '$id_c'");
        }
        
        // Borrar la cita
        mysqli_query($conexion, "DELETE FROM cita WHERE id_cita = '$id_borrar'");
        header("Location: medico_citas.php"); 
        exit;
    } else {
        echo "<script>alert('No tienes permiso para borrar esta cita.');</script>";
    }
}

// 4. PREPARAR FILTROS
$filtro_fecha = isset($_GET['fecha_filtro']) ? $_GET['fecha_filtro'] : '';
$filtro_paciente = isset($_GET['paciente_busqueda']) ? $_GET['paciente_busqueda'] : '';
$fecha_hoy = date('Y-m-d');
?>

<div class="container" style="padding: 20px;">
    
    <h2 style="text-align: center; margin-bottom: 30px; color: #2c3e50;">Panel Médico: Mis Pacientes Citados</h2>

    <div class="panel-filtro">
        <h3>Filtrar Citas</h3>
        <form method="GET" action="medico_citas.php">
            
            <div class="grupo-input">
                <a href="medico_citas.php?fecha_filtro=<?php echo $fecha_hoy; ?>" class="btn-hoy" title="Ver citas de hoy">
                   📅 Hoy
                </a>
            </div>

            <div class="grupo-input">
                <label>Fecha:</label>
                <input type="date" name="fecha_filtro" value="<?php echo $filtro_fecha; ?>">
            </div>

            <div class="grupo-input">
                <label>Paciente:</label>
                <input type="text" name="paciente_busqueda" placeholder="Nombre o Apellidos..." value="<?php echo htmlspecialchars($filtro_paciente); ?>">
            </div>

            <div class="grupo-input">
                <button type="submit" class="btn-buscar">🔍 Buscar</button>
            </div>
            
            <div class="grupo-input ver-todos">
                <a href="medico_citas.php">Ver Todos / Limpiar</a>
            </div>
        </form>
    </div>

    <table border="1">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Paciente</th>
                <th>Motivo Consulta</th>
                <th>Acciones Clínicas</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // --- CONSTRUCCIÓN DE LA CONSULTA SQL DINÁMICA ---
            
            // Condición obligatoria: Solo citas de este médico
            $where_clauses = array();
            $where_clauses[] = "c.medico_id = '$id_mi_medico'";

            // Si hay fecha seleccionada
            if (!empty($filtro_fecha)) {
                $fecha_safe = mysqli_real_escape_string($conexion, $filtro_fecha);
                $where_clauses[] = "c.fecha = '$fecha_safe'";
            }

            // Si hay búsqueda por nombre
            if (!empty($filtro_paciente)) {
                $paciente_safe = mysqli_real_escape_string($conexion, $filtro_paciente);
                $where_clauses[] = "(p.nombre LIKE '%$paciente_safe%' OR p.apellidos LIKE '%$paciente_safe%')";
            }

            // Unir condiciones
            $sql_condiciones = implode(" AND ", $where_clauses);

            // Query final
            $query = "SELECT c.*, p.nombre as p_nom, p.apellidos as p_ape
                      FROM cita c
                      INNER JOIN paciente p ON c.paciente_id = p.id_paciente
                      WHERE $sql_condiciones
                      ORDER BY c.fecha DESC, c.hora ASC";
            
            $result = mysqli_query($conexion, $query);

            // --- MOSTRAR RESULTADOS ---
            if (mysqli_num_rows($result) > 0) {
                while ($fila = mysqli_fetch_assoc($result)) {
                    // Resaltar visualmente si la cita es HOY
                    $estilo_fila = ($fila['fecha'] == $fecha_hoy) ? "style='background-color: #e6f7ff;'" : "";
                    
                    // Formato de fecha para mostrar (dd-mm-aaaa)
                    $fecha_mostrar = date("d-m-Y", strtotime($fila['fecha']));

                    echo "<tr $estilo_fila>";
                    echo "<td>".$fecha_mostrar."</td>";
                    echo "<td>".$fila['hora']."</td>";
                    echo "<td><strong>".$fila['p_ape']."</strong>, ".$fila['p_nom']."</td>";
                    echo "<td>".$fila['motivo']."</td>";
                    echo "<td>
                            <a href='admin_gestion_consulta.php?id_cita=".$fila['id_cita']."' style='font-weight:bold; color:#007bff;'>Gestionar Consulta</a> 
                            <span style='color:#ccc;'>|</span> 
                            <a href='?borrar_cita=".$fila['id_cita']."' onclick=\"return confirm('¿Seguro que quieres cancelar esta cita? Esta acción borrará recetas asociadas si existen.');\" style='color:#dc3545;'>Cancelar Cita</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center; padding: 30px; color: #666;'>No se encontraron citas con los filtros seleccionados.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include("footer.php");?>