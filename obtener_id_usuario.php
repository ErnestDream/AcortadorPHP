<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

try {
    $pdo = new PDO('mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4', 'root', 'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (empty($data['email'])) {
        echo json_encode(["success" => false, "error" => "Falta el email"]);
        exit;
    }

    $email = $data['email'];

    // Consulta para obtener el ID del usuario según el email
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        echo json_encode([
            "success" => true,
            "id" => $usuario['id']
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "error" => "Usuario no encontrado"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
