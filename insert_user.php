<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// 1. Conexión CORRECTA usando variables de Railway
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

// Conexión con MySQLi
$conn = new mysqli($host, $user, $pass, $db, $port);

// Verificar error
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Conexión fallida: " . $conn->connect_error]));
}

// 2. Recibir datos (coincidiendo con Android)
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validar campos
$required = ['id', 'nombre', 'email', 'tipo'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(["success" => false, "error" => "Falta el campo: $field"]);
        exit;
    }
}

// 3. Asignar valores correctos
$id = $data['id'];
$nombre = $data['nombre'];    // ¡Coincide con Android!
$email = $data['email'];      // ¡Coincide con Android!
$tipo = $data['tipo'];

// 4. Insertar en la base de datos
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
