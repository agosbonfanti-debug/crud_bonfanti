<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';
requiereLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método no permitido.'); }
validarToken();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('Identificador inválido.'); }
$stmt = $conexion->prepare('UPDATE disenos SET archivado_en = CURRENT_TIMESTAMP WHERE id = :id AND usuario_id = :usuario_id AND archivado_en IS NULL');
$stmt->execute(['id'=>$id, 'usuario_id'=>$_SESSION['usuario_id']]);
mensaje('exito', 'La propuesta fue archivada. Podés recuperarla cuando quieras.');
header('Location: panel.php'); exit;
