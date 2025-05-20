<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Configuración de la base de datos (cambiar por tus credenciales)
define('DB_HOST', 'mysql.railway.internal');
define('DB_NAME', 'railway');
define('DB_USER', 'root');
define('DB_PASS', 'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc');

try {
    // Conexión PDO con configuración segura
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode([
        "success" => false,
        "error" => "Error de conexión a la base de datos"
    ]));
}

try {
    // Obtener email según el método de solicitud
    $email = '';
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $email = $_GET['email'] ?? '';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';
    }

    // Validaciones básicas
    if (empty($email)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "El parámetro email es requerido"
        ]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Formato de email inválido"
        ]);
        exit;
    }

    // Consulta segura con prepared statement
    $stmt = $pdo->prepare("
        SELECT id, nombre, tipo, intentos 
        FROM usuarios 
        WHERE email = :email
        LIMIT 1
    ");
    
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        echo json_encode([
            "success" => true,
            "data" => [
                "id" => $usuario['id'],
                "nombre" => $usuario['nombre'],
                "tipo" => $usuario['tipo'],
                "intentos" => $usuario['intentos']
            ]
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Usuario no encontrado"
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error en BD: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => "Error en la base de datos"
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Error interno del servidor"
    ]);
}
