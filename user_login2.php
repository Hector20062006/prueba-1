<?php include("conexion_db.php"); ?>
<?php 
    $DNI = $_POST['DNI'];
    $password = $_POST['password'];

    $hash_passwd = hash('sha256', $password);

    $query = "SELECT * FROM usuario WHERE DNI = '$DNI' AND password_hash = '$hash_passwd'";
    $result = mysqli_query($conexion, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $usuario = mysqli_fetch_assoc($result);
        
        if ($usuario['estado'] != 'activo') {
            echo '
            <script>
                alert("ACCESO DENEGADO: Tu cuenta está pendiente de validación o ha sido desactivada.");
                window.location.href = "user_login.php"; 
            </script>';
            exit();
        }

        session_start();
        $_SESSION['DNI'] = $usuario['DNI'];
        $_SESSION['rol'] = $usuario['rol'];
        $_SESSION['medico_id'] = $usuario['medico_id'];
        $_SESSION['paciente_id'] = $usuario['paciente_id'];

        // Redirigir según el rol
        if ($usuario['rol'] == 'admin') {
            header("Location: admin.php");
            exit();
        } elseif ($usuario['rol'] == 'medico') {
            header("Location: medico_citas.php");
            exit();
        } elseif ($usuario['rol'] == 'paciente') {
            header("Location: paciente_citas.php");
            exit();
        } else {
            echo "No existe el usuario";
            echo '<br><a href="user_login.php">Volver a intentar</a>';
        }
    } else {
        echo "DNI o contraseña incorrectos.";
        echo '<br><a href="user_login.php">Volver a intentar</a>';}
?>