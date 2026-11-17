<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';
requiereLogin();
$stmt = $conexion->prepare('SELECT id, titulo, ambiente, archivado_en FROM disenos WHERE usuario_id=:id AND archivado_en IS NOT NULL ORDER BY archivado_en DESC');
$stmt->execute(['id'=>$_SESSION['usuario_id']]);
$lista = $stmt->fetchAll();
$tituloPagina = 'Mis archivados | NIDO';
require 'encabezado.php';
?>
<section class="pagina-form">
<a class="volver" href="panel.php">← Volver a mi espacio</a>
<p class="sobrelinea">HISTORIAL</p><h1>Propuestas archivadas.</h1>
<?php mostrarMensaje(); ?>
<?php if (!$lista): ?><div class="vacio"><h3>No hay propuestas archivadas.</h3><p>Cuando archives una idea, la vas a encontrar acá.</p></div>
<?php else: ?>
<div class="tabla-contenedor"><table><thead><tr><th>Propuesta</th><th>Ambiente</th><th>Fecha de archivado</th><th>Acción</th></tr></thead><tbody>
<?php foreach ($lista as $d): ?><tr><td><?= escapar($d['titulo']) ?></td><td><?= escapar($d['ambiente']) ?></td><td><?= escapar($d['archivado_en']) ?></td><td><form method="POST" action="reactivar_diseno.php"><input type="hidden" name="csrf_token" value="<?= escapar(crearToken()) ?>"><input type="hidden" name="id" value="<?= (int)$d['id'] ?>"><button class="boton-mini" type="submit">Reactivar</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
</section>
<?php require 'pie.php'; ?>
