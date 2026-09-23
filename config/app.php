<?php
declare(strict_types=1);

const APP_NAME = 'WAKTUTEMU.ID';
const APP_URL = '';
const APP_TIMEZONE = 'Asia/Jakarta';
const TEMPLATE_PATH = __DIR__ . '/../templates';

date_default_timezone_set(APP_TIMEZONE);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
}
