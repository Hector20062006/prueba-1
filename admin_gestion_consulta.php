<?php
session_start();
include("header.php");
include("conexion_db.php");
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'medico')) {
    header("Location: user_login.php");
    exit();
}

// Validar ID
if (!isset($_GET['id_cita'])) { die("Falta el ID de la cita."); }
$id_cita = $_GET['id_cita'];

// 1. BUSCAR O CREAR CONSULTA AUTOMÁTICAMENTE
// Verificamos si ya existe una fila en la tabla 'consultas' para esta cita
$check_sql = "SELECT * FROM consultas WHERE id_cita = '$id_cita'";
$check_res = mysqli_query($conexion, $check_sql);
$consulta_data = mysqli_fetch_assoc($check_res);

if (!$consulta_data) {
    // Si no existe, creamos una vacía para poder empezar a trabajar
    mysqli_query($conexion, "INSERT INTO consultas (id_cita, diagnostico, observaciones) VALUES ('$id_cita', '', '')");
    // Recargamos para obtener el ID recién creado
    header("Location: admin_gestion_consulta.php?id_cita=$id_cita");
    exit;
}
$id_consulta = $consulta_data['id_consulta']; // ID Clave para las recetas

// 2. PROCESAR FORMULARIO DE DIAGNÓSTICO
if (isset($_POST['guardar_diagnostico'])) {
    $diag = mysqli_real_escape_string($conexion, $_POST['diagnostico']);
    $obs  = mysqli_real_escape_string($conexion, $_POST['observaciones']);
    
    mysqli_query($conexion, "UPDATE consultas SET diagnostico='$diag', observaciones='$obs' WHERE id_consulta='$id_consulta'");
    $mensaje = "Diagnóstico guardado.";
    // Refrescamos datos
    $consulta_data['diagnostico'] = $diag;
    $consulta_data['observaciones'] = $obs;
}

// 3. PROCESAR NUEVA RECETA
if (isset($_POST['agregar_receta'])) {
    $med = mysqli_real_escape_string($conexion, $_POST['medicamento']);
    $dos = mysqli_real_escape_string($conexion, $_POST['dosis']);
    $ins = mysqli_real_escape_string($conexion, $_POST['instrucciones']);
    
    // Insertamos usando id_consulta (FK)
    $sql_receta = "INSERT INTO recetas (consulta_id, medicamento, dosis, instrucciones) VALUES ('$id_consulta', '$med', '$dos', '$ins')";
    mysqli_query($conexion, $sql_receta);
}

// 4. BORRAR RECETA
if (isset($_GET['borrar_receta'])) {
    $id_r = $_GET['borrar_receta'];
    mysqli_query($conexion, "DELETE FROM recetas WHERE id_receta='$id_r'");
    header("Location: admin_gestion_consulta.php?id_cita=$id_cita"); // Limpiar URL
    exit;
}

// DATOS INFORMATIVOS (Paciente y Médico)
$info_cita = mysqli_query($conexion, "SELECT c.fecha, p.nombre as pnom, p.apellidos as pape, m.apellidos as doc 
                                      FROM cita c 
                                      JOIN paciente p ON c.paciente_id = p.id_paciente
                                      JOIN medico m ON c.medico_id = m.id_medico
                                      WHERE c.id_cita = '$id_cita'");
$info = mysqli_fetch_assoc($info_cita);
?>

<a href="admin_citas.php">← Volver al Listado</a>
<h1>Atención de Cita #<?php echo $id_cita; ?></h1>
<p>
    <strong>Paciente:</strong> <?php echo $info['pnom']." ".$info['pape']; ?> | 
    <strong>Doctor:</strong> <?php echo $info['doc']; ?> | 
    <strong>Fecha:</strong> <?php echo $info['fecha']; ?>
</p>

<hr>

<div>
    <h3>1. Diagnóstico y Observaciones</h3>
    <?php if(isset($mensaje)) echo "<b>$mensaje</b>"; ?>
    
    <form method="POST">
        <label>Diagnóstico:</label><br>
        <textarea name="diagnostico" rows="5"><?php echo $consulta_data['diagnostico']; ?></textarea>
        <br>
        <label>Observaciones:</label><br>
        <textarea name="observaciones" rows="3"><?php echo $consulta_data['observaciones']; ?></textarea>
        <br><br>
        <input type="submit" name="guardar_diagnostico" value="Guardar Cambios Clínicos">
    </form>
</div>

<br>

<div>
    <h3>2. Recetas / Medicamentos</h3>
    
    <table border="1">
        <tr>
            <th>Medicamento</th>
            <th>Dosis</th>
            <th>Instrucciones</th>
            <th>Acción</th>
        </tr>
        <?php
        $q_recetas = mysqli_query($conexion, "SELECT * FROM recetas WHERE consulta_id = '$id_consulta'");
        while($r = mysqli_fetch_assoc($q_recetas)){
            echo "<tr>";
            echo "<td>".$r['medicamento']."</td>";
            echo "<td>".$r['dosis']."</td>";
            echo "<td>".$r['instrucciones']."</td>";
            echo "<td><a href='?id_cita=$id_cita&borrar_receta=".$r['id_receta']."'><img src='imgs/papelera.png' alt='Borrar' height='20'></a></td>";
            echo "</tr>";
        }
        ?>
    </table>
    
    <br>
    
    <h4>Agregar Medicamento</h4>
    <form method="POST">
        <input type="text" name="medicamento" placeholder="Nombre Medicamento" required>
        <input type="text" name="dosis" placeholder="Dosis (ej: 500mg)" required>
        <input type="text" name="instrucciones" placeholder="Cada 8 horas..." required>
        <input type="submit" name="agregar_receta" value="Añadir">
    </form>
</div>