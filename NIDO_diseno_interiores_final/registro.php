<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';
if (usuarioConectado()) { header('Location: panel.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarToken();
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $clave = $_POST['password'] ?? '';

    if ($nombre === '' || $apellido === '' || $dni === '' || $email === '' || $clave === '') {
        $error = 'Completá todos los campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ingresá un correo electrónico válido.';
    } elseif (!preg_match('/^[0-9]{7,8}$/', $dni)) {
        $error = 'El DNI debe tener 7 u 8 números.';
    } elseif (strlen($clave) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } else {
        $check = $conexion->prepare('SELECT id FROM usuarios WHERE dni = :dni OR email = :email LIMIT 1');
        $check->execute(['dni' => $dni, 'email' => $email]);
        if ($check->fetch()) {
            $error = 'Ya existe una cuenta con ese DNI o correo.';
        } else {
            $hash = password_hash($clave, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, apellido, dni, email, password, rol) VALUES (:nombre, :apellido, :dni, :email, :password, 'usuario')");
            $stmt->execute(['nombre'=>$nombre, 'apellido'=>$apellido, 'dni'=>$dni, 'email'=>$email, 'password'=>$hash]);
            mensaje('exito', 'Tu cuenta se creó correctamente. Ya podés ingresar.');
            header('Location: login.php'); exit;
        }
    }
}
$tituloPagina = 'Crear cuenta | NIDO';
require 'encabezado.php';
?>
<section class="form-layout">
    <div class="form-lateral">
        <p class="sobrelinea">BIENVENIDO A NIDO</p>
        <h1>Empezá a darle forma a tus ideas.</h1>
        <p>Creá tu cuenta para guardar tus propuestas de diseño y tenerlas siempre a mano.</p>
        <img src="uploads/productos/detalle.svg" alt="Detalle decorativo de interiorismo">
    </div>
    <div class="form-caja">
        <p class="sobrelinea">TU CUENTA</p><h2>Crear cuenta</h2>
        <p class="texto-suave">Completá tus datos para registrarte.</p>
        <?php if ($error): ?><div class="alerta error"><?= escapar($error) ?></div><?php endif; ?>
        <form method="POST" class="formulario">
            <input type="hidden" name="csrf_token" value="<?= escapar(crearToken()) ?>">
            <div class="dos-columnas">
                <label>Nombre<input name="nombre" maxlength="80" required value="<?= escapar($_POST['nombre'] ?? '') ?>"></label>
                <label>Apellido<input name="apellido" maxlength="80" required value="<?= escapar($_POST['apellido'] ?? '') ?>"></label>
            </div>
            <label>DNI<input name="dni" inputmode="numeric" maxlength="8" required value="<?= escapar($_POST['dni'] ?? '') ?>"></label>
            <label>Correo electrónico<input type="email" name="email" maxlength="120" required value="<?= escapar($_POST['email'] ?? '') ?>"></label>
            <label>Contraseña<input type="password" name="password" minlength="8" required></label>
            <button class="boton ancho" type="submit">Crear mi cuenta</button>
        </form>
        <p class="texto-pie-form">¿Ya tenés cuenta? <a href="login.php">Ingresá acá</a></p>
    </div>
</section>
<?php require 'pie.php'; ?>
