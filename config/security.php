<?php
declare(strict_types=1);

function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function verify_csrf(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) { http_response_code(419); exit('Sesi formulir kedaluwarsa.'); }
}
function admin_required(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['admin_id'])) { header('Location: /admin/login'); exit; }
}
function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
