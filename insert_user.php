<?php
// Configuración de cabeceras para API REST
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Conexión a la base de datos MySQL usando PDO
$pdo = new PDO('mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4', 
	       'root',
	       'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc'
	      );

// Configura PDO para que lance excepciones en errores SQL
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtiene el método HTTP utilizado en la solicitud
$method = $_SERVER['REQUEST_METHOD'];

// Solo acepta solicitudes POST
if ($method === 'POST') {
    // Lee el cuerpo de la solicitud en formato JSON
    $json = file_get_contents('php://input');
    // Decodifica el JSON a un array asociativo
    $data = json_decode($json, true);

    // Validar campos requeridos
    $required = ['nombre', 'email', 'tipo', 'intentos'];
    $missing = [];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            $missing[] = $field;
        }
    }

    // Si hay campos faltantes, devuelve error 400
    if (!empty($missing)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "error" => "Campos faltantes: " . implode(', ', $missing)
        ]);
        exit;
    }

    // Asignar valores
    $nombre = (string) ($data['nombre'] ?? '');
    $email = (string) ($data['email'] ?? '');
    $tipo = (string) ($data['tipo'] ?? '');
    $intentos = $data['intentos'];

    /**
     * Verifica si el usuario ya existe en la base de datos
     * usando el email como identificador único
     */
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $existe = $stmt->fetchColumn(); // Obtiene el número de coincidencias

    // Si el usuario existe (count > 0), devuelve error
    if ($existe > 0) {
        echo json_encode([
            "success" => false,
            "message" => "El usuario ya existe"
        ]);
        exit;
    }

    /**
     * Intenta insertar el nuevo usuario en la base de datos
     * - Usa prepared statements para seguridad contra SQL injection
     * - Cláusula ON DUPLICATE KEY para actualizar el tipo si el email existe
     */
    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, tipo, intentos)
                               VALUES (?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE tipo = VALUES(tipo)");
        $stmt->execute([$nombre, $email, $tipo, $intentos]);

	// Respuesta exitosa con los datos del usuario creado
        echo json_encode([
            "success" => true,
            "message" => "Usuario insertado o actualizado",
            "data" => [
                "nombre" => $nombre,
                "email" => $email,
                "tipo" => $tipo,
                "intentos" => $intentos
            ]
        ]);

    } catch (PDOException $e) {
	// Manejo de errores de base de datos
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => "Error en la base de datos: " . $e->getMessage()
        ]);
    }

} else {
    // Respuesta para métodos HTTP no permitidos (distintos de POST)
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Método no permitido"]);
}
?>
