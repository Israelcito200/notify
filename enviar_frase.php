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

  // Selecciona una frase aleatoria
  $stmt = $db->query("SELECT * FROM frases ORDER BY RAND() LIMIT 1");
  $frase = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$frase || empty($frase['texto'])) {
    echo "No hay frases disponibles para enviar.";
    exit;
  }

  // Mensaje directo solo con el texto
  $mensaje = $frase['texto'];

  // Datos para Pushover
  $data = [
    'token'   => getenv('PUSHOVER_TOKEN'),
    'user'    => getenv('PUSHOVER_USER'),
    'message' => $mensaje,
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

 

} catch (PDOException $e) {
  echo "❌ Error de conexión: " . $e->getMessage();
  exit(1);
}
