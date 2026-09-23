<?php
if (!function_exists('whatsapp_url')) {
    function whatsapp_url(string $number, string $message): string {
        $phone = preg_replace('/[^0-9]/', '', $number);
        if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
    }
}
