<?php
$servidor = "192.168.2.11:3306";
$usuario = "root";
$password = "root";
$base_datos= "hospital_db";

$conexion=new mysqli($servidor,$usuario,$password, $base_datos);
?>