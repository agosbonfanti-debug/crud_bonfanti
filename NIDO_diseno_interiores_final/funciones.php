<?php
// Funciones que se reutilizan en distintas páginas del proyecto.

function escapar($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function usuarioConectado(): bool {
    return isset($_SESSION['usuario_id']);
}

function requiereLogin(): void {
    if (!usuarioConectado()) {
        header('Location: login.php');
        exit;
    }
}

function requiereAdmin(): void {
    requiereLogin();
    if (($_SESSION['usuario_rol'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('No tenés permisos para entrar a esta sección.');
    }
}

function crearToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validarToken(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('La solicitud no pudo ser validada. Volvé a intentarlo.');
    }
}

function mensaje($tipo, $texto): void {
    $_SESSION['mensaje'] = ['tipo' => $tipo, 'texto' => $texto];
}

function mostrarMensaje(): void {
    if (!empty($_SESSION['mensaje'])) {
        $m = $_SESSION['mensaje'];
        echo '<div class="alerta ' . escapar($m['tipo']) . '">' . escapar($m['texto']) . '</div>';
        unset($_SESSION['mensaje']);
    }
}
