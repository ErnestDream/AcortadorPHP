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

    // Establece tipo = 'Premium' e intentos = -1 (representando "ilimitados")
    $stmt = $pdo->prepare("UPDATE usuarios SET tipo = 'Premium', intentos = -1 WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Usuario actualizado a Premium con intentos ilimitados"]);
    } else {
        echo json_encode(["success" => false, "message" => "No se actualizó el usuario"]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
