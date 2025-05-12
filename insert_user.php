<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Conexión a la base de datos (usa las variables de entorno de Railway)
$host = getenv('MYSQLHOST');
$port = getenv('MYSQLPORT');
$db   = getenv('MYSQLDATABASE');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Conexión fallida: " . $conn->connect_error]));
}

// Obtener el contenido POST como JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validar datos recibidos
if (!$data || !isset($data['id'], $data['personName'], $data['personEmail'], $data['tipo'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos"]);
    exit;
}

$id = $data['id'];
$nombre = $data['personName'];
$email = $data['personEmail'];
$tipo = $data['tipo'];

// Insertar usuario
$sql = "INSERT INTO usuarios (id, nombre, email, tipo) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $id, $nombre, $email, $tipo);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$conn->close();
?>
