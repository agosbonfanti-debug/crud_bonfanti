
<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';

requiereAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

validarToken();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Identificador inválido.');
}

if ($id === (int)$_SESSION['usuario_id']) {
    mensaje('error', 'No podés eliminar tu propia cuenta.');
    header('Location: admin_usuarios.php');
    exit;
}

try {
    $conexion->beginTransaction();

    // Primero eliminamos los diseños del usuario.
    $stmt = $conexion->prepare(
        'DELETE FROM disenos WHERE usuario_id = :id'
    );
    $stmt->execute(['id' => $id]);

    // Después eliminamos el usuario.
    $stmt = $conexion->prepare(
        "DELETE FROM usuarios WHERE id = :id AND rol <> 'admin'"
    );
    $stmt->execute(['id' => $id]);

    $conexion->commit();

    mensaje('exito', 'Usuario eliminado definitivamente.');

} catch (PDOException $e) {
    $conexion->rollBack();
    mensaje('error', 'No se pudo eliminar el usuario.');
}

header('Location: admin_usuarios.php');
exit;
