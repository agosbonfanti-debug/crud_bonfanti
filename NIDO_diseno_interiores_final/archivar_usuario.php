<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';
requiereAdmin();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Método no permitido.');}
validarToken();$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if(!$id||$id<1){http_response_code(400);exit('Identificador inválido.');}
if($id===$_SESSION['usuario_id']){mensaje('error','No podés archivar tu propia cuenta mientras estás conectado.');header('Location: admin_usuarios.php');exit;}
$u=$conexion->prepare("UPDATE usuarios SET archivado_en=CURRENT_TIMESTAMP WHERE id=:id AND archivado_en IS NULL AND rol<>'admin'");$u->execute(['id'=>$id]);
mensaje('exito','La cuenta fue archivada si cumplía las condiciones.');header('Location: admin_usuarios.php');exit;
