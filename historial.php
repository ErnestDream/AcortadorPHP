<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$pdo = new PDO('mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4', 'root', 'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!isset($data['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email no proporcionado.']);
        exit;
    }

    $email = $data['email'];

    // Obtener idUsuario a partir del email
    $stmt = $pdo->prepare("SELECT idUsuario FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado.']);
        exit;
    }

    $idUsuario = $usuario['idUsuario'];

    // Obtener historial de URLs
    $stmt = $pdo->prepare("SELECT slug, url FROM urls WHERE idUsuario = ?");
    $stmt->execute([$idUsuario]);
    $urls = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'urls' => $urls]);
}
?>
