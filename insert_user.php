<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$pdo = new PDO('mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4', 'root', 'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 2. Recibir datos (coincidiendo con Android)
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validar campos
$required = ['nombre', 'email', 'tipo'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(["success" => false, "error" => "Falta el campo: $field"]);
        exit;
    }
}

// 3. Asignar valores correctos
$nombre = $data['nombre'];    // ¡Coincide con Android!
$email = $data['email'];      // ¡Coincide con Android!
$tipo = $data['tipo'];

// 4. Insertar en la base de datos
$sql = "INSERT INTO usuarios (nombre, email, tipo) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nombre, $email, $tipo);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$conn->close();
?>
