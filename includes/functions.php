<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';

function money(int|float $value): string { return 'Rp' . number_format((float)$value, 0, ',', '.'); }
function slugify(string $value): string { $value = strtolower(trim($value)); $value = preg_replace('/[^a-z0-9]+/i', '-', $value) ?? ''; return trim($value, '-'); }
function setting(string $key, string $fallback = ''): string { static $settings = null; if ($settings === null) { try { $settings = db()->query('SELECT setting_key, setting_value FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR); } catch (Throwable) { $settings = []; } } return (string)($settings[$key] ?? $fallback); }
function template_config(string $slug): ?array { $file = TEMPLATE_PATH . '/' . basename($slug) . '/template.json'; if (!is_file($file)) return null; $data = json_decode((string)file_get_contents($file), true); return is_array($data) ? $data : null; }
function json_template(string $slug): ?array { return template_config($slug); }
function template_catalog(): array {
	$items = [];
	foreach (glob(TEMPLATE_PATH . '/*/template.json') ?: [] as $file) {
		$data = json_decode((string)file_get_contents($file), true);
		if (is_array($data) && !empty($data['meta']['slug'])) $items[] = $data;
	}
	usort($items, static fn(array $a, array $b): int => (int)($a['meta']['sort'] ?? 99) <=> (int)($b['meta']['sort'] ?? 99));
	return $items;
}
function url(string $path = ''): string { return '/' . ltrim($path, '/'); }
function rupiah(int|float $value): string { return money($value); }
function clean_guest(string $value): string { return trim(mb_substr(preg_replace('/[^\p{L}\p{N} .,&\-]/u', '', $value) ?? '', 0, 100)); }
function template_url(string $slug): string { return '/demo/' . rawurlencode($slug); }
function public_url(string $path = ''): string { $base = APP_URL ?: ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost'); return rtrim($base, '/') . '/' . ltrim($path, '/'); }
function safe_url(string $url): string { return filter_var($url, FILTER_VALIDATE_URL) ? $url : '#'; }
function order_id(): string { return 'WTM-' . date('Ymd') . '-' . str_pad((string)random_int(1, 9999), 4, '0', STR_PAD_LEFT); }
function whatsapp_url(string $phone, string $message): string { $digits = preg_replace('/\D+/', '', $phone) ?? ''; if (str_starts_with($digits, '0')) $digits = '62' . substr($digits, 1); return 'https://wa.me/' . $digits . '?text=' . rawurlencode($message); }
function sample_wedding(): array { return ['bride' => ['name' => 'Aisyah', 'full_name' => 'Aisyah Putri'], 'groom' => ['name' => 'Akmal', 'full_name' => 'Akmal Pratama'], 'date' => '31 December 2026', 'slug' => 'aisyah-akmal']; }
function invitation_data(?array $invitation, array $template): array {
	if (!$invitation || empty($invitation['id'])) return $template;
	try {
		$pdo = db(); $data = $template;
		$q = $pdo->prepare('SELECT * FROM couples WHERE invitation_id=? LIMIT 1'); $q->execute([$invitation['id']]); $couple = $q->fetch();
		if ($couple) { $data['couple']['bride']['name']=$couple['bride_name']; $data['couple']['bride']['full_name']=$couple['bride_full_name']; $data['couple']['groom']['name']=$couple['groom_name']; $data['couple']['groom']['full_name']=$couple['groom_full_name']; $data['hero']['couple']=$couple['bride_name'].' & '.$couple['groom_name']; $data['opening']['bride']=$couple['bride_name']; $data['opening']['groom']=$couple['groom_name']; }
		$q = $pdo->prepare('SELECT title,event_date AS date,event_time AS time,venue,address,maps_url AS maps FROM events WHERE invitation_id=? AND status=1 ORDER BY sort_order,id'); $q->execute([$invitation['id']]); $events=$q->fetchAll(); if($events) $data['events']=$events;
		if (!empty($invitation['wedding_date'])) { $date=date('d F Y',strtotime($invitation['wedding_date'])); $data['hero']['date']=$date; $data['opening']['date']=date('d.m.y',strtotime($invitation['wedding_date'])); }
		return $data;
	} catch (Throwable) { return $template; }
}
