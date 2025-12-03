<?php 
include("header.php"); 
include("conexion_db.php");
?>

<h2>Registro de Nuevo Usuario</h2>

<!-- Contenedor principal con clase CSS, sin estilos en línea -->
<div class="formulario-registro">

    <?php 
    // PASO 1: SELECCIÓN DE ROL
    if (!isset($_GET['tipo'])) { 
    ?>
        <div class="contenedor-opciones">
            <h3>¿Cómo deseas registrarte?</h3>
            <br>
            
            <div class="opciones-rol">
                
                <a href="register.php?tipo=paciente" class="btn-rol paciente">
                    Soy<br><strong>Paciente</strong>
                </a>

                <a href="register.php?tipo=medico" class="btn-rol medico">
                    Soy<br><strong>Médico</strong>
                </a>

            </div>
            
            <br><br>
            <p>Si eres médico, deberás adjuntar tu Currículum para validación.</p>
        </div>

    <?php 
    // PASO 2: FORMULARIO PARA PACIENTE
    } elseif ($_GET['tipo'] == 'paciente') { 
    ?>
        <h3>Registro de Paciente</h3>
        <form action="register_insert.php" method="POST">
            <input type="hidden" name="rol" value="paciente">
            
            <label>DNI:</label>
            <input type="text" name="dni" required>

            <label>Contraseña:</label>
            <input type="password" name="password" required>

            <hr>

            <label>Nombre:</label>
            <input type="text" name="nombre" required maxlength="9">

            <label>Apellidos:</label>
            <input type="text" name="apellidos" required maxlength="30">

            <label>Fecha de Nacimiento:</label>
            <input type="date" name="fecha_nacimiento" required>

            <label>Email:</label>
            <input type="email" name="email" required maxlength="30">

            <label>Teléfono:</label>
            <input type="tel" name="telefono" required maxlength="15">

            <label>Dirección:</label>
            <input type="text" name="direccion" required maxlength="40">

            <label>Afecciones / Alergias:</label>
            <textarea name="afecciones"></textarea>

            <input type="submit" name="registro_paciente" value="Completar Registro">
            
            <div class="contenedor-volver">
                <a href="register.php" class="btn-volver">&larr; Volver atrás</a>
            </div>
        </form>

    <?php 
    // PASO 3: FORMULARIO PARA MÉDICO
    } elseif ($_GET['tipo'] == 'medico') { 
    ?>
        <h3>Registro de Personal Médico</h3>
        <form action="register_insert.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="rol" value="medico">

            <label>DNI:</label>
            <input type="text" name="dni" required maxlength="9">

            <label>Contraseña:</label>
            <input type="password" name="password" required maxlength="20">

            <hr>

            <label>Nombre:</label>
            <input type="text" name="nombre" required mazlength="20">

            <label>Apellidos:</label>
            <input type="text" name="apellidos" required maxlength="30">

            <label>Especialidad:</label>
            <select name="especialidad" required maxlength="30">
                <option value="">-- Seleccione --</option>
                <?php
                $q_esp = mysqli_query($conexion, "SELECT * FROM especialidad ORDER BY nombre");
                while($row = mysqli_fetch_assoc($q_esp)) {
                    echo "<option value='".$row['id_especialidad']."'>".$row['nombre']."</option>";
                }
                ?>
            </select>

            <label>Email:</label>
            <input type="email" name="email" required maxlength="30">

            <label>Teléfono:</label>
            <input type="tel" name="telefono" required maxlength="9">

            <input type="submit" name="registro_medico" value="Enviar Solicitud">
            
            <div class="contenedor-volver">
                <a href="register.php" class="btn-volver">&larr; Volver atrás</a>
            </div>
        </form>
    <?php } ?>

</div>

<?php include("footer.php"); ?>