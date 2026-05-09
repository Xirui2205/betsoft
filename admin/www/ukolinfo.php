<?php
$id_kalendar = $_GET["id_kalendar"];
$id_user = $_GET["id_user"];
$ukol[0]["id"] = $id_kalendar;
$ukol[0]["id_user"] = $id_user;
$ukol[0]["cas"] = "15:00";
$ukol[0]["odkud"] = "choto";

header('Content-Type: application/json');
echo json_encode($ukol);
?>