<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';
if (usuarioConectado()) { header('Location: panel.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarToken();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $clave = $_POST['password'] ?? '';
    $stmt = $conexion->prepare('SELECT id, nombre, apellido, email, password, rol FROM usuarios WHERE email = :email AND archivado_en IS NULL LIMIT 1');
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch();
    if ($usuario && password_verify($clave, $usuario['password'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int)$usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_apellido'] = $usuario['apellido'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        header('Location: panel.php'); exit;
    }
    $error = 'El correo o la contraseña no son correctos.';
}
$tituloPagina = 'Ingresar NIDO';
require 'encabezado.php';
?>
<section class="form-layout login-layout">
    <div class="form-lateral">
        <p class="sobrelinea">QUÉ BUENO TENERTE DE VUELTA</p>
        <h1>Tus ideas te están esperando.</h1>
        <p>Entrá a tu espacio y seguí trabajando en los ambientes que imaginaste.</p>
        <img src="uploads/productos/detalle.svg" alt="Ilustración decorativa">
    </div>
    <div class="form-caja">
        <p class="sobrelinea">TU ESPACIO PERSONAL</p><h2>Iniciar sesión</h2>
        <p class="texto-suave">Ingresá con tu correo y contraseña.</p>
        <?php mostrarMensaje(); ?>
        <?php if ($error): ?><div class="alerta error"><?= escapar($error) ?></div><?php endif; ?>
        <form method="POST" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= escapar(crearToken()) ?>">
            <label>Correo electrónico<input type="email" name="email" required value="<?= escapar($_POST['email'] ?? '') ?>"></label>
            <label>Contraseña<input type="password" name="password" required></label>
            <button class="boton ancho" type="submit">Ingresar</button>
        </form>
        <p class="texto-pie-form">¿Todavía no tenés cuenta? <a href="registro.php">Registrate</a></p>
    </div>
</section>
<?php require 'pie.php'; ?>
