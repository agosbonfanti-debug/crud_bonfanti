<?php
// Usá este archivo una sola vez para crear la primera cuenta administradora.
// Después de crearla, eliminá crear_admin.php del proyecto.
session_start();
require_once 'conexion.php';
require_once 'funciones.php';
$cuenta = $conexion->query("SELECT COUNT(*) FROM usuarios WHERE rol='admin'")->fetchColumn();
$mensaje = '';
if ((int)$cuenta > 0) {
    exit('Ya existe un administrador. Por seguridad, eliminá este archivo.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarToken();
    $nombre=trim($_POST['nombre']??'');$apellido=trim($_POST['apellido']??'');$dni=trim($_POST['dni']??'');$email=strtolower(trim($_POST['email']??''));$clave=$_POST['password']??'';
    if($nombre===''||$apellido===''||$dni===''||$email===''||strlen($clave)<8){$mensaje='Completá todos los datos. La contraseña debe tener al menos 8 caracteres.';}
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)||!preg_match('/^[0-9]{7,8}$/',$dni)){$mensaje='Revisá el correo y el DNI.';}
    else{
        $hash=password_hash($clave,PASSWORD_DEFAULT);
        $stmt=$conexion->prepare("INSERT INTO usuarios (nombre,apellido,dni,email,password,rol) VALUES (:n,:a,:d,:e,:p,'admin')");
        try{$stmt->execute(['n'=>$nombre,'a'=>$apellido,'d'=>$dni,'e'=>$email,'p'=>$hash]);$mensaje='Administrador creado. Ahora eliminá crear_admin.php y entrá desde login.php.';}
        catch(PDOException $e){$mensaje='No se pudo crear. Revisá que el DNI y el correo no estén registrados.';}
    }
}
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Crear administrador NIDO</title><link rel="stylesheet" href="css/estilos.css"></head><body><main class="contenedor"><section class="pagina-form"><p class="sobrelinea">CONFIGURACIÓN INICIAL</p><h1>Crear administrador</h1><p>Este formulario sirve para crear la primera cuenta con permisos de administración.</p><?php if($mensaje):?><div class="alerta"><?=escapar($mensaje)?></div><?php endif;?><form method="POST" class="formulario formulario-grande"><input type="hidden" name="csrf_token" value="<?=escapar(crearToken())?>"><label>Nombre<input name="nombre" required></label><label>Apellido<input name="apellido" required></label><label>DNI<input name="dni" required></label><label>Correo<input type="email" name="email" required></label><label>Contraseña<input type="password" name="password" minlength="8" required></label><button class="boton" type="submit">Crear administrador</button></form></section></main></body></html>
