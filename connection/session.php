<?php
/**
 * Controle de sessão e autenticação.
 * Perfis usados no sistema:
 * V = Voluntário
 * I = Instituição
 * F = Funcionário
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool {
    return isset($_SESSION['user']) && isset($_SESSION['user']['tipo']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_role(string $tipo): void {
    require_login();
    if (($_SESSION['user']['tipo'] ?? '') !== $tipo) {
        http_response_code(403);
        exit('Acesso negado.');
    }
}

function h(?string $valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function flash_set(string $tipo, string $mensagem): void {
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

function flash_get(): ?array {
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function current_role_name(): string {
    return match (current_user()['tipo'] ?? '') {
        'I' => 'Instituição',
        'F' => 'Funcionário',
        default => 'Voluntário',
    };
}

function logout_user(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
