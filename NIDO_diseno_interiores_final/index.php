<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';
$tituloPagina = 'NIDO Tu casa, tu estilo';
require 'encabezado.php';
?>
<section class="portada">
    <div class="portada-texto">
        <p class="sobrelinea">DISEÑO DE INTERIORES A TU MANERA</p>
        <h1>Tu casa.<br><em>Tu forma de sentirla.</em></h1>
        <p class="bajada">Ideas, colores y detalles para transformar cada rincón en un lugar que hable de vos.</p>
        <div class="acciones">
            <?php if (usuarioConectado()): ?>
                <a class="boton" href="nuevo_diseno.php">Crear mi propuesta</a>
                <a class="boton boton-claro" href="panel.php">Ver mi espacio</a>
            <?php else: ?>
                <a class="boton" href="registro.php">Empezar mi proyecto</a>
                <a class="boton boton-claro" href="login.php">Ya tengo una cuenta</a>
            <?php endif; ?>
        </div>
        <div class="nota-portada"><span>01</span> Diseñá espacios que se sientan como hogar.</div>
    </div>
    <div class="portada-foto">
        <img src="uploads/productos/portada.svg" alt="Ilustración de un living cálido en tonos naturales">
        <div class="etiqueta-foto">Espacios con identidad</div>
    </div>
</section>

<section class="intro">
    <div>
        <p class="sobrelinea">UN PROYECTO CON MUCHAS POSIBILIDADES</p>
        <h2>¿Cómo te gustaría<br>que se vea tu hogar?</h2>
    </div>
    <p>Contanos qué ambiente querés renovar, qué estilo te gusta y qué colores imaginás. Podés guardar tus ideas y volver a ellas cuando quieras.</p>
</section>

<section class="tarjetas">
    <article class="tarjeta">
        <img src="uploads/productos/living.svg" alt="Living de estilo natural">
        <div><span>01 / AMBIENTES</span><h3>Espacios para compartir</h3><p>Livings, comedores y rincones para disfrutar.</p></div>
    </article>
    <article class="tarjeta">
        <img src="uploads/productos/dormitorio.svg" alt="Dormitorio en tonos suaves">
        <div><span>02 / DESCANSO</span><h3>Tu lugar de calma</h3><p>Dormitorios pensados para sentirse bien.</p></div>
    </article>
    <article class="tarjeta">
        <img src="uploads/productos/cocina.svg" alt="Cocina con madera y plantas">
        <div><span>03 / DETALLES</span><h3>Pequeños cambios</h3><p>Materiales, colores y objetos que hacen la diferencia.</p></div>
    </article>
</section>

<section class="franja">
    <p>“Los espacios también cuentan historias.”</p>
    <a href="<?= usuarioConectado() ? 'nuevo_diseno.php' : 'registro.php' ?>">Contanos la tuya <span>↗</span></a>
</section>
<?php require 'pie.php'; ?>
