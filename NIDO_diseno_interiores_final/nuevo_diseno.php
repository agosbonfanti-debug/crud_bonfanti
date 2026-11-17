<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';
requiereLogin();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarToken();
    $titulo = trim($_POST['titulo'] ?? '');
    $ambiente = trim($_POST['ambiente'] ?? '');
    $estilo = trim($_POST['estilo'] ?? '');
    $colores = trim($_POST['colores'] ?? '');
    $presupuesto = trim($_POST['presupuesto'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $imagen = null;

    if ($titulo === '' || $ambiente === '' || $estilo === '' || $colores === '' || $presupuesto === '' || $descripcion === '') {
        $error = 'Completá todos los campos obligatorios.';
    } elseif (strlen($titulo) > 120 || strlen($descripcion) > 2000) {
        $error = 'Revisá la extensión del título o de la descripción.';
    } else {
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $resultado = guardarImagen($_FILES['imagen']);
            if (!$resultado['ok']) { $error = $resultado['error']; }
            else { $imagen = $resultado['ruta']; }
        }
        if ($error === '') {
            $stmt = $conexion->prepare('INSERT INTO disenos (usuario_id, titulo, ambiente, estilo, colores, presupuesto, descripcion, imagen) VALUES (:usuario_id, :titulo, :ambiente, :estilo, :colores, :presupuesto, :descripcion, :imagen)');
            $stmt->execute(['usuario_id'=>$_SESSION['usuario_id'], 'titulo'=>$titulo, 'ambiente'=>$ambiente, 'estilo'=>$estilo, 'colores'=>$colores, 'presupuesto'=>$presupuesto, 'descripcion'=>$descripcion, 'imagen'=>$imagen]);
            mensaje('exito', 'Tu propuesta quedó guardada.');
            header('Location: panel.php'); exit;
        }
    }
}
function guardarImagen(array $archivo): array {
    if ($archivo['error'] !== UPLOAD_ERR_OK) return ['ok'=>false, 'error'=>'No se pudo recibir la imagen.'];
    $maximoBytes = 2 * 1024 * 1024;
    if ($archivo['size'] > $maximoBytes) return ['ok'=>false, 'error'=>'La imagen no puede superar los 2 MB.'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $tipoReal = $finfo->file($archivo['tmp_name']);
    $tiposPermitidos = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
    if (!isset($tiposPermitidos[$tipoReal])) return ['ok'=>false, 'error'=>'Formato no permitido. Usá JPG, PNG o WEBP.'];
    $extension = $tiposPermitidos[$tipoReal];
    $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $extension;
    $carpeta = __DIR__ . '/uploads/productos/';
    if (!is_dir($carpeta)) mkdir($carpeta, 0755, true);
    if (!move_uploaded_file($archivo['tmp_name'], $carpeta . $nombreArchivo)) return ['ok'=>false, 'error'=>'No se pudo guardar la imagen.'];
    return ['ok'=>true, 'ruta'=>'uploads/productos/' . $nombreArchivo];
}
$tituloPagina = 'Nueva propuesta | NIDO';
require 'encabezado.php';
?>
<section class="pagina-form">
    <a class="volver" href="panel.php">← Volver a mi espacio</a>
    <p class="sobrelinea">CONTANOS TU IDEA</p><h1>Armemos tu próximo espacio.</h1>
    <p class="texto-suave">No hace falta tener todo decidido. Empezá por contarnos lo que imaginás.</p>
    <?php if ($error): ?><div class="alerta error"><?= escapar($error) ?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="formulario formulario-grande">
        <input type="hidden" name="csrf_token" value="<?= escapar(crearToken()) ?>">
        <div class="form-bloque-titulo"><span>01</span><div><h2>Sobre el ambiente</h2><p>¿Qué lugar te gustaría transformar?</p></div></div>
        <label>Nombre de tu propuesta<input name="titulo" maxlength="120" placeholder="Ej. Mi living soñado" required value="<?= escapar($_POST['titulo'] ?? '') ?>"></label>
        <div class="dos-columnas">
            <label>Tipo de ambiente<select name="ambiente" required><option value="">Elegir ambiente</option><?php foreach (['Living','Dormitorio','Cocina','Comedor','Baño','Escritorio','Exterior','Otro'] as $op): ?><option <?= (($_POST['ambiente'] ?? '') === $op) ? 'selected' : '' ?>><?= escapar($op) ?></option><?php endforeach; ?></select></label>
            <label>Estilo que te gusta<select name="estilo" required><option value="">Elegir estilo</option><?php foreach (['Minimalista','Nórdico','Rústico','Industrial','Moderno','Boho','Clásico','Japandi','Natural'] as $op): ?><option <?= (($_POST['estilo'] ?? '') === $op) ? 'selected' : '' ?>><?= escapar($op) ?></option><?php endforeach; ?></select></label>
        </div>
        <div class="form-bloque-titulo"><span>02</span><div><h2>La estética</h2><p>Elegí los tonos y la inversión aproximada.</p></div></div>
        <div class="dos-columnas">
            <label>Colores que imaginás<input name="colores" maxlength="120" placeholder="Ej. Beige, verde oliva y madera" required value="<?= escapar($_POST['colores'] ?? '') ?>"></label>
            <label>Presupuesto aproximado<select name="presupuesto" required><option value="">Elegir presupuesto</option><?php foreach (['Menos de $200.000','$200.000 a $500.000','$500.000 a $1.000.000','Más de $1.000.000','Todavía no lo definí'] as $op): ?><option <?= (($_POST['presupuesto'] ?? '') === $op) ? 'selected' : '' ?>><?= escapar($op) ?></option><?php endforeach; ?></select></label>
        </div>
        <label>Contanos un poco más<textarea name="descripcion" rows="5" maxlength="2000" placeholder="¿Qué te gustaría cambiar? ¿Qué cosas querés conservar? ¿Qué sensación te gustaría que transmita el ambiente?" required><?= escapar($_POST['descripcion'] ?? '') ?></textarea></label>
        <div class="form-bloque-titulo"><span>03</span><div><h2>Imagen de inspiración</h2><p>Podés subir una foto o referencia para acompañar tu idea.</p></div></div>
        <label class="subida">Seleccionar imagen<input type="file" name="imagen" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG o WEBP · Máximo 2 MB · Campo opcional</small></label>
        <button class="boton" type="submit">Guardar mi propuesta</button>
    </form>
</section>
<?php require 'pie.php'; ?>
