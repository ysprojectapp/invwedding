<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/whatsapp.php';

$path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/');
$base = trim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
if ($base && str_starts_with($path, $base)) $path = trim(substr($path, strlen($base)), '/');
$parts = $path === '' ? [] : explode('/', $path);
$action = $parts[0] ?? '';

switch ($action) {
    case 'templates':
        $templates = template_catalog();
        require __DIR__ . '/views/templates.php';
        break;
    case 'demo':
    case 'template':
        $slug = $parts[1] ?? '';
        $template = json_template($slug);
        if (!$template) { http_response_code(404); require __DIR__ . '/views/404.php'; exit; }
        $demo = $action === 'demo';
        require __DIR__ . '/views/template-detail.php';
        break;
    case 'checkout':
        $slug = $parts[1] ?? '';
        $template = json_template($slug);
        if (!$template) { http_response_code(404); require __DIR__ . '/views/404.php'; exit; }
        require __DIR__ . '/views/checkout.php';
        break;
    case 'invitation':
        $slug = $parts[1] ?? '';
        $guest = clean_guest($_GET['to'] ?? '');
        $invitation = null;
        try { $q = db()->prepare('SELECT i.*, t.slug AS template_slug FROM invitations i JOIN templates t ON t.id = i.template_id WHERE i.slug = ? AND i.status = "active" LIMIT 1'); $q->execute([$slug]); $invitation = $q->fetch(); } catch (Throwable) {}
        if (!$invitation) { http_response_code(404); require __DIR__ . '/views/404.php'; exit; }
        $template = json_template($invitation['template_slug']);
        require __DIR__ . '/views/invitation.php';
        break;
    case 'share':
        $slug = $parts[1] ?? '';
        $template = json_template('elegant-rose');
        require __DIR__ . '/views/share.php';
        break;
    case 'order':
        require __DIR__ . '/views/order.php';
        break;
    case 'api':
        $endpoint = $parts[1] ?? '';
        if (in_array($endpoint, ['rsvp', 'wishes'], true)) require __DIR__ . '/api/' . $endpoint . '.php';
        else { http_response_code(404); require __DIR__ . '/views/404.php'; }
        break;
    case 'calendar':
        $_GET['slug'] = $parts[1] ?? '';
        require __DIR__ . '/calendar.php';
        break;
    case 'admin':
        if (($parts[1] ?? '') === 'login') require __DIR__ . '/admin/login.php';
        elseif (($parts[1] ?? '') === 'logout') require __DIR__ . '/admin/logout.php';
        else require __DIR__ . '/admin/index.php';
        break;
    default:
        if ($action !== '') {
            $slug = $action;
            $guest = clean_guest($_GET['to'] ?? '');
            $invitation = null;
            try { $q = db()->prepare('SELECT i.*, t.slug AS template_slug FROM invitations i JOIN templates t ON t.id = i.template_id WHERE i.slug = ? AND i.status = "active" LIMIT 1'); $q->execute([$slug]); $invitation = $q->fetch(); } catch (Throwable) {}
            if (!$invitation) { http_response_code(404); require __DIR__ . '/views/404.php'; exit; }
            $template = json_template($invitation['template_slug']);
            require __DIR__ . '/views/invitation.php';
        } else {
            $templates = template_catalog();
            require __DIR__ . '/views/home.php';
        }
}
