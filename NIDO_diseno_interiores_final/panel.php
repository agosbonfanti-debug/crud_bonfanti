<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';
requiereLogin();
$stmt = $conexion->prepare('SELECT id, titulo, ambiente, estilo, colores, presupuesto, imagen, creado_en FROM disenos WHERE usuario_id = :id AND archivado_en IS NULL ORDER BY creado_en DESC');
$stmt->execute(['id' => $_SESSION['usuario_id']]);
$disenos = $stmt->fetchAll();
$tituloPagina = 'Mi espacio | NIDO';
require 'encabezado.php';
?>
<section class="cabecera-panel">
    <div><p class="sobrelinea">MI ESPACIO</p><h1>Hola, <?= escapar($_SESSION['usuario_nombre']) ?>.</h1><p>Estas son las ideas que fuiste guardando para tu hogar.</p></div>
    <a class="boton" href="nuevo_diseno.php">+ Nueva propuesta</a>
</section>
<?php mostrarMensaje(); ?>
<section class="resumen">
    <div><span>PROPUESTAS ACTIVAS</span><strong><?= count($disenos) ?></strong></div>
    <div><span>CUENTA</span><strong>Personal</strong></div>
    <div><span>ESTADO</span><strong class="estado-texto">En marcha</strong></div>
</section>
<div class="titulo-seccion"><div><p class="sobrelinea">TUS PROYECTOS</p><h2>Mis propuestas</h2></div></div>
<?php if (!$disenos): ?>
    <div class="vacio"><span>✳</span><h3>Todavía no guardaste ninguna idea.</h3><p>Cuando completes tu primera propuesta, va a aparecer acá.</p><a class="boton" href="nuevo_diseno.php">Crear propuesta</a></div>
<?php else: ?>
    <section class="grilla-disenos">
    <?php foreach ($disenos as $d): ?>
        <article class="diseno-card">
            <?php if ($d['imagen']): ?><img src="<?= escapar($d['imagen']) ?>" alt="Imagen de inspiración para <?= escapar($d['titulo']) ?>"><?php else: ?><div class="sin-imagen">NIDO / IDEAS</div><?php endif; ?>
            <div class="diseno-info"><span class="tag"><?= escapar($d['ambiente']) ?></span><h3><?= escapar($d['titulo']) ?></h3><p><?= escapar($d['estilo']) ?> · <?= escapar($d['colores']) ?></p>
                <div class="card-acciones"><a href="editar_diseno.php?id=<?= (int)$d['id'] ?>">Editar</a>
                <form method="POST" action="archivar_diseno.php" class="form-inline" onsubmit="return confirm('¿Querés archivar esta propuesta?');">
                    <input type="hidden" name="csrf_token" value="<?= escapar(crearToken()) ?>"><input type="hidden" name="id" value="<?= (int)$d['id'] ?>"><button type="submit" class="link-peligro">Archivar</button>
                </form></div>
            </div>
        </article>
    <?php endforeach; ?>
    </section>
<?php endif; ?>
<div class="panel-enlaces">
    <a href="mis_archivados.php">Ver mis propuestas archivadas</a>
    <?php if (($_SESSION['usuario_rol'] ?? '') === 'admin'): ?><a href="admin_usuarios.php">Ir al panel administrativo</a><?php endif; ?>
</div>
<?php require 'pie.php'; ?>
