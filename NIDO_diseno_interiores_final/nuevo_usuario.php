<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';
requiereAdmin();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarToken();
    $nombre=trim($_POST['nombre']??''); $apellido=trim($_POST['apellido']??''); $dni=trim($_POST['dni']??''); $email=strtolower(trim($_POST['email']??'')); $clave=$_POST['password']??''; $rol=$_POST['rol']??'usuario';
    if ($nombre===''||$apellido===''||$dni===''||$email===''||$clave==='') $error='Completá todos los campos.';
    elseif (!filter_var($email,FILTER_VALIDATE_EMAIL)) $error='Correo electrónico inválido.';
    elseif (!preg_match('/^[0-9]{7,8}$/',$dni)) $error='El DNI debe tener 7 u 8 números.';
    elseif (strlen($clave)<8) $error='La contraseña debe tener al menos 8 caracteres.';
    elseif (!in_array($rol,['usuario','admin'],true)) $error='Rol inválido.';
    else {
        $c=$conexion->prepare('SELECT id FROM usuarios WHERE dni=:dni OR email=:email LIMIT 1'); $c->execute(['dni'=>$dni,'email'=>$email]);
        if ($c->fetch()) $error='Ya existe una cuenta con ese DNI o correo.';
        else { $hash=password_hash($clave,PASSWORD_DEFAULT); $i=$conexion->prepare('INSERT INTO usuarios (nombre,apellido,dni,email,password,rol) VALUES (:n,:a,:d,:e,:p,:r)'); $i->execute(['n'=>$nombre,'a'=>$apellido,'d'=>$dni,'e'=>$email,'p'=>$hash,'r'=>$rol]); mensaje('exito','El usuario fue registrado.'); header('Location: admin_usuarios.php'); exit; }
    }
}
$tituloPagina='Nuevo usuario | NIDO'; require 'encabezado.php';
?>
<section class="pagina-form"><a class="volver" href="admin_usuarios.php">← Volver a usuarios</a><p class="sobrelinea">ADMINISTRACIÓN</p><h1>Registrar usuario.</h1>
<?php if($error):?><div class="alerta error"><?=escapar($error)?></div><?php endif;?>
<form method="POST" class="formulario formulario-grande"><input type="hidden" name="csrf_token" value="<?=escapar(crearToken())?>">
<div class="dos-columnas"><label>Nombre<input name="nombre" required maxlength="80"></label><label>Apellido<input name="apellido" required maxlength="80"></label></div>
<div class="dos-columnas"><label>DNI<input name="dni" required maxlength="8"></label><label>Correo<input type="email" name="email" required maxlength="120"></label></div>
<div class="dos-columnas"><label>Contraseña<input type="password" name="password" minlength="8" required></label><label>Rol<select name="rol"><option value="usuario">Usuario</option><option value="admin">Administrador</option></select></label></div>
<button class="boton" type="submit">Guardar usuario</button></form></section>
<?php require 'pie.php'; ?>
