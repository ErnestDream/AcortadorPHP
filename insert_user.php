<?php
// Conexión a la base de datos (ajusta con tus credenciales de Railway)
$host = 'mysql.railway.internal';
$db = 'railway';
$user = 'root';
$pass = 'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recoger datos POST
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$tipo = $_POST['tipo'];

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
