<?php
session_start();require_once 'conexion.php';require_once 'funciones.php';requiereAdmin();
$q=$conexion->query('SELECT id,nombre,apellido,dni,email,rol,archivado_en FROM usuarios WHERE archivado_en IS NOT NULL ORDER BY archivado_en DESC');$usuarios=$q->fetchAll();
$tituloPagina='Usuarios archivados | NIDO';require 'encabezado.php';
?>
<section class="pagina-form"><a class="volver" href="admin_usuarios.php">← Volver a usuarios</a><p class="sobrelinea">HISTORIAL</p><h1>Usuarios archivados.</h1>
<?php if(!$usuarios):?><div class="vacio"><h3>No hay usuarios archivados.</h3></div><?php else:?>
<div class="tabla-contenedor"><table><thead><tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>DNI</th><th>Correo</th><th>Archivado</th><th>Acción</th></tr></thead><tbody>
<?php foreach($usuarios as $u):?><tr><td><?=(int)$u['id']?></td><td><?=escapar($u['nombre'])?></td><td><?=escapar($u['apellido'])?></td><td><?=escapar($u['dni'])?></td><td><?=escapar($u['email'])?></td><td><?=escapar($u['archivado_en'])?></td><td><form method="POST" action="reactivar_usuario.php"><input type="hidden" name="csrf_token" value="<?=escapar(crearToken())?>"><input type="hidden" name="id" value="<?=(int)$u['id']?>"><button class="boton-mini" type="submit">Reactivar</button></form></td></tr><?php endforeach;?>
</tbody></table></div><?php endif;?></section>
<?php require 'pie.php';?>
