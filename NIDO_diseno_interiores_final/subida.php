<?php
// Valida la imagen desde el servidor antes de guardarla.
function guardarImagenSegura(array $archivo): array {
    if ($archivo['error'] !== UPLOAD_ERR_OK) return ['ok'=>false, 'error'=>'No se pudo recibir la imagen.'];
    if ($archivo['size'] > 2 * 1024 * 1024) return ['ok'=>false, 'error'=>'La imagen no puede superar los 2 MB.'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $tipoReal = $finfo->file($archivo['tmp_name']);
    $permitidos = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
    if (!isset($permitidos[$tipoReal])) return ['ok'=>false, 'error'=>'Formato no permitido. Usá JPG, PNG o WEBP.'];
    $nombre = bin2hex(random_bytes(16)) . '.' . $permitidos[$tipoReal];
    $destino = __DIR__ . '/uploads/productos/' . $nombre;
    if (!move_uploaded_file($archivo['tmp_name'], $destino)) return ['ok'=>false, 'error'=>'No se pudo guardar la imagen.'];
    return ['ok'=>true, 'ruta'=>'uploads/productos/' . $nombre];
}
