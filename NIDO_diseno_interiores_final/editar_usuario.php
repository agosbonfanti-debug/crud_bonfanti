<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';
requiereAdmin();
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if(!$id||$id<1){http_response_code(400);exit('Identificador inválido.');}
$q=$conexion->prepare('SELECT id,nombre,apellido,dni,email FROM usuarios WHERE id=:id AND archivado_en IS NULL LIMIT 1');$q->execute(['id'=>$id]);$u=$q->fetch();
if(!$u){http_response_code(404);exit('Usuario no encontrado.');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 validarToken();$nombre=trim($_POST['nombre']??'');$apellido=trim($_POST['apellido']??'');$dni=trim($_POST['dni']??'');$email=strtolower(trim($_POST['email']??''));
 if($nombre===''||$apellido===''||$dni===''||$email==='')$error='Completá todos los campos.';
 elseif(!filter_var($email,FILTER_VALIDATE_EMAIL))$error='Correo inválido.';
 elseif(!preg_match('/^[0-9]{7,8}$/',$dni))$error='DNI inválido.';
 else{
  $dup=$conexion->prepare('SELECT id FROM usuarios WHERE (dni=:dni OR email=:email) AND id<>:id LIMIT 1');$dup->execute(['dni'=>$dni,'email'=>$email,'id'=>$id]);
  if($dup->fetch())$error='Ese DNI o correo ya está siendo utilizado.';
  else{$up=$conexion->prepare('UPDATE usuarios SET nombre=:n,apellido=:a,dni=:d,email=:e WHERE id=:id AND archivado_en IS NULL');$up->execute(['n'=>$nombre,'a'=>$apellido,'d'=>$dni,'e'=>$email,'id'=>$id]);mensaje('exito','Los datos se actualizaron.');header('Location: admin_usuarios.php');exit;}
 }
}
$tituloPagina='Editar usuario | NIDO';require 'encabezado.php';
?>
<section class="pagina-form"><a class="volver" href="admin_usuarios.php">← Volver a usuarios</a><p class="sobrelinea">ADMINISTRACIÓN</p><h1>Editar usuario.</h1>
<?php if($error):?><div class="alerta error"><?=escapar($error)?></div><?php endif;?>
<form method="POST" class="formulario formulario-grande"><input type="hidden" name="csrf_token" value="<?=escapar(crearToken())?>">
<div class="dos-columnas"><label>Nombre<input name="nombre" required value="<?=escapar($_POST['nombre']??$u['nombre'])?>"></label><label>Apellido<input name="apellido" required value="<?=escapar($_POST['apellido']??$u['apellido'])?>"></label></div>
<div class="dos-columnas"><label>DNI<input name="dni" required value="<?=escapar($_POST['dni']??$u['dni'])?>"></label><label>Correo<input type="email" name="email" required value="<?=escapar($_POST['email']??$u['email'])?>"></label></div>
<p class="texto-suave">La contraseña y el rol no se modifican desde este formulario.</p><button class="boton" type="submit">Guardar cambios</button></form></section>
<?php require 'pie.php'; ?>
