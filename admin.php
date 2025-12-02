<?php
session_start();
include("admin_header.php");
include("admin_logic.php");
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: user_login.php");
    exit();
}
?>

<h1>Panel de Administración de Usuarios</h1>

<!-- Formulario para crear usuario -->
<form action="admin_logic.php" method="post">
    <h3>Crear nuevo usuario</h3>
    <input type="text" name="dni" placeholder="DNI" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <select name="rol">
        <option value="medico">Médico</option>
        <option value="admin">Admin</option>
        <option value="paciente">Paciente</option>
    </select>
    <input type="submit" name="crear" value="Crear usuario">
</form>

<hr>

<!-- Formulario de búsqueda -->
<form method="POST">
    <h3>Buscar usuario por DNI, Rol o ID Médico</h3>
    <input type="text" name="termino_busqueda" placeholder="Ejemplo: 12345678A o admin o 5" value="<?php echo htmlspecialchars($busqueda); ?>">
    <input type="submit" name="buscar" value="Buscar">
    <input type="submit" name="mostrar_todos" value="Mostrar todos">
</form>

<br>
<hr>
<br>
<!-- Tabla de usuarios -->
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>DNI</th>
        <th>Rol</th>
        <th>Médico ID</th>
        <th>Paciente ID</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>

    <?php while ($fila = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <form method="POST">
            <td>
                <input type="hidden" name="id_usuario" value="<?php echo $fila['id_usuario']; ?>">
                <?php 
                if($fila['rol'] == 'medico' || $fila['rol'] == 'paciente' ) {
                    echo '<a href="admin_usuario_info.php?id=' . $fila['id_usuario'] . '">' . $fila['id_usuario'] . '</a>';
                } else {
                    echo $fila['id_usuario'];
                }
                ?>
            </td>
            <td><input type="text" name="dni" value="<?php echo $fila['DNI']; ?>"></td>
            <td>
                <select name="rol">
                    <option value="admin" <?php if($fila['rol'] == 'admin') echo 'selected'; ?>>Admin</option>
                    <option value="medico" <?php if($fila['rol'] == 'medico') echo 'selected'; ?>>Médico</option>
                    <option value="paciente" <?php if($fila['rol'] == 'paciente') echo 'selected'; ?>>Paciente</option>
                </select>
            </td>
            <td><input type="number" name="medico_id" value="<?php echo $fila['medico_id']; ?>"></td>
            <td><input type="number" name="paciente_id" value="<?php echo $fila['paciente_id']; ?>"></td>
            <td>
                <select name="estado">
                    <option value="activo" <?php if($fila['estado'] == 'activo') echo 'selected'; ?>>Activo</option>
                    <option value="pendiente" <?php if($fila['estado'] == 'pendiente') echo 'selected'; ?>>Pendiente</option>
                    <option value="inactivo" <?php if($fila['estado'] == 'inactivo') echo 'selected'; ?>>Deshabilitado</option>
                </select>
            <td>
                <input type="password" name="password" placeholder="Nueva contraseña (opcional)">
                <input type="submit" name="actualizar" value="Actualizar">
                <a href="admin_panel.php?borrar=<?php echo $fila['id_usuario']; ?>" 
                onclick="return confirm('¿Estás seguro de que quieres borrar este usuario?');">
                <img src="imgs/papelera.png" alt="Borrar" height="20"></a>
            </td>
        </form>
    </tr>
    <?php } ?>
</table>

<?php include("footer.php"); ?>

