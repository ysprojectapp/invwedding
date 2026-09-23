<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'message'=>'Method tidak diizinkan']); exit; }
$input = json_decode((string)file_get_contents('php://input'), true) ?: $_POST;
$invitation = clean_guest((string)($input['invitation'] ?? ''));
$name = clean_guest((string)($input['name'] ?? ''));
$whatsapp = trim((string)($input['whatsapp'] ?? ''));
$attendance = (string)($input['attendance'] ?? '');
$count = max(1, min(10, (int)($input['guest_count'] ?? 1)));
if (!$invitation || !$name || !in_array($attendance, ['hadir','tidak_hadir','ragu'], true) || ($whatsapp && !preg_match('/^[0-9+() -]{8,20}$/', $whatsapp))) { http_response_code(422); echo json_encode(['ok'=>false,'message'=>'Data RSVP belum valid.']); exit; }
try {
    $q = db()->prepare('SELECT id FROM invitations WHERE slug=? AND status="active" LIMIT 1'); $q->execute([$invitation]); $invitationId = (int)$q->fetchColumn();
    if (!$invitationId) throw new RuntimeException('Invitation tidak ditemukan.');
    $q = db()->prepare('INSERT INTO rsvps(invitation_id,guest_name,whatsapp,attendance,guest_count) VALUES(?,?,?,?,?)'); $q->execute([$invitationId,$name,$whatsapp ?: null,$attendance,$count]);
    echo json_encode(['ok'=>true,'message'=>'RSVP berhasil dikirim.']);
} catch (Throwable) { http_response_code(500); echo json_encode(['ok'=>false,'message'=>'RSVP belum dapat disimpan.']); }
