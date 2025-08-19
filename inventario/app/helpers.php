<?php
declare(strict_types=1);

function base_url(): string {
    $dir = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    return $dir === '/' ? '' : $dir; // p.ej. /inventario/public
}
function url(string $path = '/'): string {
    if ($path === '') $path = '/';
    if ($path[0] !== '/') $path = '/'.$path;
    return base_url() . $path;
}
function redirect(string $path): void {
    $target = (preg_match('#^https?://#', $path)) ? $path : url($path);
    header('Location: ' . $target);
    exit;
}
function is_post(): bool { return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'; }
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!current_user()) redirect('/login'); }
function require_role(string $role): void {
    require_login();
    $u = current_user();
    if (strtoupper($u['rol'] ?? '') !== strtoupper($role)) {
        flash('danger', 'No autorizado.');
        redirect('/inventario');
    }
}
function flash(string $type, string $message): void { $_SESSION['flash'][$type][] = $message; }
function get_flashes(): array { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
