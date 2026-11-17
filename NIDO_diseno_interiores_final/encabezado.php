<?php
// Este encabezado se incluye en las páginas que necesitan la misma navegación.
$tituloPagina = $tituloPagina ?? 'NIDO | Diseño de interiores';
$prefijo = ''; 
$linkAdmin = 'admin_usuarios.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escapar($tituloPagina) ?></title>
    <link rel="stylesheet" href="<?= escapar($prefijo) ?>css/estilos.css">
</head>
<body>
<header class="barra">
    <a class="marca" href="<?= escapar($prefijo) ?>index.php"><span class="marca-icono">n.</span><span>NIDO<small>DISEÑO DE INTERIORES</small></span></a>
    <nav>
        <a href="<?= escapar($prefijo) ?>index.php">Inicio</a>
        <?php if (usuarioConectado()): ?>
            <a href="<?= escapar($prefijo) ?>panel.php">Mi espacio</a>
            <?php if (($_SESSION['usuario_rol'] ?? '') === 'admin'): ?>
                <a href="<?= escapar($linkAdmin) ?>">Administración</a>
            <?php endif; ?>
            <a class="nav-boton" href="<?= escapar($prefijo) ?>cerrar_sesion.php">Salir</a>
        <?php else: ?>
            <a href="<?= escapar($prefijo) ?>login.php">Ingresar</a>
            <a class="nav-boton" href="<?= escapar($prefijo) ?>registro.php">Crear cuenta</a>
        <?php endif; ?>
    </nav>
</header>
<main class="contenedor">
