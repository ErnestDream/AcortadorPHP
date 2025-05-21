<?php
// Configuración de cabeceras HTTP para API REST
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Conexión a la BD por medio de PDO
$pdo = new PDO(
    'mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4', 
    'root', 
    'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc'
);

// Configura PDO para lanzar excepciones en errores SQL (mejor manejo de errores)
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Obtener email según el método de solicitud
    $email = '';
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $email = $_GET['email'] ?? '';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';
    }

    // Validación 1: Campo email no vacío
    if (empty($email)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "El parámetro email es requerido"
        ]);
        exit;
    }
    
    // Validación 2: Formato de email válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Formato de email inválido"
        ]);
        exit;
    }

    /**
     * Consulta preparada para seguridad contra SQL Injection
     * - Selecciona: id, nombre, tipo e intentos
     * - Filtra por email (parámetro :email)
     * - LIMIT 1 para obtener solo un resultado
     */
    $stmt = $pdo->prepare("
        SELECT id, nombre, tipo, intentos 
        FROM usuarios 
        WHERE email = :email
        LIMIT 1
    ");

    // Ejecuta la consulta con el parámetro email
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    //Respuesta de la API
    if ($usuario) {
        // Caso éxito: Usuario encontrado
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
        // Caso error: Usuario no existe
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "Usuario no encontrado"
        ]);
    }

} catch (PDOException $e) {
    // Error específico de la base de datos
    http_response_code(500);
    error_log("Error en BD: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => "Error en la base de datos"
    ]);
} catch (Exception $e) {
    // Error genérico del servidor
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Error interno del servidor"
    ]);
}
