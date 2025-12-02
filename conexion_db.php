<?php
$servidor = "127.0.0.1:3306";
$usuario = "usuario_hospital";
$password = "password123";
$base_datos= "hospital_db";

$conexion=new mysqli($servidor,$usuario,$password, $base_datos);
?>