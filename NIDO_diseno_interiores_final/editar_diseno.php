<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';
requiereLogin();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('Identificador inválido.'); }
$stmt = $conexion->prepare('SELECT * FROM disenos WHERE id = :id AND usuario_id = :usuario_id AND archivado_en IS NULL LIMIT 1');
$stmt->execute(['id'=>$id, 'usuario_id'=>$_SESSION['usuario_id']]);
$diseno = $stmt->fetch();
if (!$diseno) { http_response_code(404); exit('No encontramos esa propuesta.'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarToken();
    $titulo = trim($_POST['titulo'] ?? '');
    $ambiente = trim($_POST['ambiente'] ?? '');
    $estilo = trim($_POST['estilo'] ?? '');
    $colores = trim($_POST['colores'] ?? '');
    $presupuesto = trim($_POST['presupuesto'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    if ($titulo === '' || $ambiente === '' || $estilo === '' || $colores === '' || $presupuesto === '' || $descripcion === '') {
        $error = 'Completá todos los campos obligatorios.';
    } else {
        $imagen = $diseno['imagen'];
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            require_once 'subida.php';
            $resultado = guardarImagenSegura($_FILES['imagen']);
            if (!$resultado['ok']) $error = $resultado['error'];
            else $imagen = $resultado['ruta'];
        }
        if ($error === '') {
            $up = $conexion->prepare('UPDATE disenos SET titulo=:titulo, ambiente=:ambiente, estilo=:estilo, colores=:colores, presupuesto=:presupuesto, descripcion=:descripcion, imagen=:imagen WHERE id=:id AND usuario_id=:usuario_id AND archivado_en IS NULL');
            $up->execute(['titulo'=>$titulo,'ambiente'=>$ambiente,'estilo'=>$estilo,'colores'=>$colores,'presupuesto'=>$presupuesto,'descripcion'=>$descripcion,'imagen'=>$imagen,'id'=>$id,'usuario_id'=>$_SESSION['usuario_id']]);
            mensaje('exito', 'Los cambios se guardaron correctamente.');
            header('Location: panel.php'); exit;
        }
    }
}
$tituloPagina = 'Editar propuesta | NIDO';
require 'encabezado.php';
?>
<section class="pagina-form">
    <a class="volver" href="panel.php">← Volver a mi espacio</a>
    <p class="sobrelinea">ACTUALIZAR IDEA</p><h1>Editar propuesta.</h1>
    <?php if ($error): ?><div class="alerta error"><?= escapar($error) ?></div><?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="formulario formulario-grande">
        <input type="hidden" name="csrf_token" value="<?= escapar(crearToken()) ?>">
        <label>Nombre de tu propuesta<input name="titulo" maxlength="120" required value="<?= escapar($_POST['titulo'] ?? $diseno['titulo']) ?>"></label>
        <div class="dos-columnas">
            <label>Tipo de ambiente<select name="ambiente" required><?php foreach (['Living','Dormitorio','Cocina','Comedor','Baño','Escritorio','Exterior','Otro'] as $op): ?><option <?= (($_POST['ambiente'] ?? $diseno['ambiente']) === $op) ? 'selected' : '' ?>><?= escapar($op) ?></option><?php endforeach; ?></select></label>
            <label>Estilo<select name="estilo" required><?php foreach (['Minimalista','Nórdico','Rústico','Industrial','Moderno','Boho','Clásico','Japandi','Natural'] as $op): ?><option <?= (($_POST['estilo'] ?? $diseno['estilo']) === $op) ? 'selected' : '' ?>><?= escapar($op) ?></option><?php endforeach; ?></select></label>
        </div>
        <div class="dos-columnas">
            <label>Colores<input name="colores" required value="<?= escapar($_POST['colores'] ?? $diseno['colores']) ?>"></label>
            <label>Presupuesto<select name="presupuesto" required><?php foreach (['Menos de $200.000','$200.000 a $500.000','$500.000 a $1.000.000','Más de $1.000.000','Todavía no lo definí'] as $op): ?><option <?= (($_POST['presupuesto'] ?? $diseno['presupuesto']) === $op) ? 'selected' : '' ?>><?= escapar($op) ?></option><?php endforeach; ?></select></label>
        </div>
        <label>Descripción<textarea name="descripcion" rows="5" maxlength="2000" required><?= escapar($_POST['descripcion'] ?? $diseno['descripcion']) ?></textarea></label>
        <?php if ($diseno['imagen']): ?><p class="texto-suave">Imagen actual:</p><img class="imagen-actual" src="<?= escapar($diseno['imagen']) ?>" alt="Imagen actual"><?php endif; ?>
        <label class="subida">Cambiar imagen (opcional)<input type="file" name="imagen" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG o WEBP · Máximo 2 MB</small></label>
        <button class="boton" type="submit">Guardar cambios</button>
    </form>
</section>
<?php require 'pie.php'; ?>
