<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Conexión a la base de datos (usando variables de entorno de Railway)

$db   = getenv('MYSQL_DATABASE');
$publicURL = getenv('MYSQL_PUBLIC_URL');
$rootPW = getenv('MYSQL_ROOT_PASSWORD');
$url = getenv('MYSQL_URL');
$Mydb = getenv('MYSQLDATABASE');
$host = getenv('MYSQLHOST');
$pass = getenv('MYSQLPASSWORD');
$port = getenv('MYSQLPORT');
$user = getenv('MYSQLUSER');

$conn = new mysqli($db, $publicURL, $rootPW, $url, $Mydb, $host, $pass, $port, $user);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Conexión fallida: " . $conn->connect_error]));
}

// Obtener el contenido POST como JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validar datos recibidos
if (!$data) {
    echo json_encode(["success" => false, "error" => "Datos no recibidos"]);
    exit;
}

// Asignar valores con comprobación
$id = $data['id'] ?? null;
$nombre = $data['personName'] ?? null;
$email = $data['personEmail'] ?? null;
$tipo = $data['tipo'] ?? null;

if (!$id || !$nombre || !$email || !$tipo) {
    echo json_encode(["success" => false, "error" => "Datos incompletos"]);
    exit;
}

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
