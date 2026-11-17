<?php
session_start();require_once 'conexion.php';require_once 'funciones.php';requiereAdmin();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Método no permitido.');}
validarToken();$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);if(!$id||$id<1){http_response_code(400);exit('Identificador inválido.');}
$u=$conexion->prepare('UPDATE usuarios SET archivado_en=NULL WHERE id=:id AND archivado_en IS NOT NULL');$u->execute(['id'=>$id]);
mensaje('exito','La cuenta fue reactivada.');header('Location: usuarios_archivados.php');exit;
