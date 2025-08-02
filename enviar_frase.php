<?php
try {
  $db = new PDO(
    'mysql:host=' . getenv('DB_HOST') . 
    ';port=' . getenv('DB_PORT') . 
    ';dbname=' . getenv('DB_NAME'),
    getenv('DB_USER'),
    getenv('DB_PASS')
  );
  $db->exec("SET NAMES 'utf8mb4'");

  $stmt = $db->query("SELECT * FROM frases ORDER BY RAND() LIMIT 1");
  $frase = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$frase) {
    echo "No hay frases nuevas para publicar.";
    exit;
  }

  $data = [
    'token'   => getenv('PUSHOVER_TOKEN'),
    'user'    => getenv('PUSHOVER_USER'),
    'message' => $frase['texto'] . ($frase['autor'] ? " — " . $frase['autor'] : ""),
    'title'   => '✨ Frase del día ✨',
    'sound'   => 'spacealarm'
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

  // Marcar como publicada
  $db->prepare("UPDATE frases SET publicada = 1 WHERE id = ?")->execute([$frase['id']]);

} catch (PDOException $e) {
  echo "❌ Error de conexión: " . $e->getMessage();
  exit(1);
}

