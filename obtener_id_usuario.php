<?php
// recibir JSON
$data = json_decode(file_get_contents("php://input"));

// conexión a BD (ajusta según tu configuración)
$conn = new mysqli($servername, $username, $password, $dbname);

$email = $conn->real_escape_string($data->email);

$sql = "SELECT id FROM usuarios WHERE email = '$email' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['id' => $row['id']]);
} else {
    echo json_encode(['id' => null]);
}

$conn->close();
?>
