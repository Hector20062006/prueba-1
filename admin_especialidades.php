<?php 
    session_start();
    include("conexion_db.php");
    include ("admin_logica_especialidades.php"); 
    include("admin_header.php");
    // Solo accesible para administradores
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
        header("Location: user_login.php");
        exit();
    }
?>
    <h2>Administrar Especialidades</h2>

    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>ID Especialidad</th>
                <th>Nombre Especialidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (mysqli_num_rows($especialidades_result) > 0) {
                while ($especialidad = mysqli_fetch_assoc($especialidades_result)) { 
            ?>
            <tr>
                <form method="POST" action="">
                    
                    <td>
                        <?php echo $especialidad['id_especialidad']; ?>
                        <input type="hidden" name="id_especialidad" value="<?php echo $especialidad['id_especialidad']; ?>">
                    </td>

                    <td>
                        <input type="text" name="nombre" value="<?php echo $especialidad['nombre']; ?>">
                    </td>

                    <td>
                        <input type="submit" name="actualizar_especialidad" value="Actualizar">
                        
                        <a href="?borrar_especialidad=<?php echo $especialidad['id_especialidad']; ?>" 
                           onclick="return confirm('¿Estás seguro de que quieres borrar esta especialidad?');">
                            <img src="imgs/papelera.png" alt="Borrar" height="20">
                        </a>
                    </td>
                </form>
            </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='3'>No hay especialidades registradas.</td></tr>";
            }
            ?>
        </tbody>
    </table>

