<?php
ob_start();
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'medico') 
    { header("Location: user_login.php"); exit(); }
include("admin_header.php");
include("conexion_db.php");

// Lógica para borrar cita desde el mismo archivo
if (isset($_GET['borrar_cita'])) {
    $id_borrar = $_GET['borrar_cita'];
    // Borramos primero las dependencias si no tienes ON DELETE CASCADE
    // 1. Buscar si hay consulta asociada
    $busca_cons = mysqli_query($conexion, "SELECT id_consulta FROM consultas WHERE id_cita = '$id_borrar'");
    if($cons = mysqli_fetch_assoc($busca_cons)){
        $id_c = $cons['id_consulta'];
        mysqli_query($conexion, "DELETE FROM recetas WHERE consulta_id = '$id_c'");
        mysqli_query($conexion, "DELETE FROM consultas WHERE id_consulta = '$id_c'");
    }
    
    $sql_borrar = "DELETE FROM cita WHERE id_cita = '$id_borrar'";
    if(mysqli_query($conexion, $sql_borrar)){
        header("Location: admin_citas.php");
        exit;
    } else {
        echo "<p>Error: " . mysqli_error($conexion) . "</p>";
    }
}
?>

<h2>Gestión de Citas Médicas</h2>

<a href="admin_form_cita.php">+ Nueva Cita</a>
<br><br>

<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha / Hora</th>
            <th>Paciente</th>
            <th>Médico</th>
            <th>Especialidad</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Hacemos JOIN para traer nombres en vez de números
        $query = "SELECT c.*, 
                         p.nombre as p_nom, p.apellidos as p_ape, 
                         m.nombre as m_nom, m.apellidos as m_ape,
                         e.nombre as especialidad
                  FROM cita c
                  LEFT JOIN paciente p ON c.paciente_id = p.id_paciente
                  LEFT JOIN medico m ON c.medico_id = m.id_medico
                  LEFT JOIN especialidad e ON c.especialidad_id = e.id_especialidad
                  ORDER BY c.fecha DESC, c.hora ASC";
        
        $result = mysqli_query($conexion, $query);

        while ($fila = mysqli_fetch_assoc($result)) {
            $fechaFormato = date("d/m/Y", strtotime($fila['fecha']));
        ?>
        <tr>
            <td><?php echo $fila['id_cita']; ?></td>
            <td><?php echo $fechaFormato . " - " . $fila['hora']; ?></td>
            <td><?php echo $fila['p_nom'] . " " . $fila['p_ape']; ?></td> 
            <td>Dr. <?php echo $fila['m_nom'] . " " . $fila['m_ape']; ?></td>
            <td><?php echo $fila['especialidad']; ?></td>
            <td>
                <a href="admin_form_cita.php?id=<?php echo $fila['id_cita']; ?>">Editar</a> | 

                <a href="admin_gestion_consulta.php?id_cita=<?php echo $fila['id_cita']; ?>"><img src="imgs/portapapeles.png" alt="Borrar" height="20"> Consulta</a> | 

                     <a href="admin_citas.php?borrar_cita=<?php echo $fila['id_cita']; ?>" 
                         onclick="return confirm('¿Borrar cita? Se eliminarán diagnósticos y recetas asociadas.');"><img src="imgs/papelera.png" alt="Borrar" height="20"></a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>