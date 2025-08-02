<?php
$db = new PDO('mysql:host=localhost;dbname=pruebas', 'root', 'DvCGxub8@');
$db->exec("SET NAMES 'utf8mb4'");
$frase = $db->query("SELECT texto FROM frases ORDER BY RAND() LIMIT 1")->fetchColumn();

if (!$frase) {
  echo "No se encontró ninguna frase para enviar.";
  exit;
}

$data = [
  $pushoverToken = getenv('PUSHOVER_TOKEN'),
  $pushoverUser = getenv('PUSHOVER_USER'),
  'message' => $frase,
  'title'   => '✨ Frase del día ✨',
  'sound'   => 'spacealarm', // Puedes probar otros como 'spacealarm' o 'tugboat'
];

$options = [
  'http' => [
    'method'  => 'POST',
    'header'  => 'Content-Type: application/x-www-form-urlencoded',
    'content' => http_build_query($data)
  ]
];

$context = stream_context_create($options);
$result = file_get_contents('https://api.pushover.net/1/messages.json', false, $context);
echo $result;
?>

