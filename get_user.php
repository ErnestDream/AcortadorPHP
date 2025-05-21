<?php
// Establece el tipo de contenido de la respuesta como JSON
header("Content-Type: application/json");

// Permite solicitudes CORS desde cualquier origen (*)
header("Access-Control-Allow-Origin: *");

// Conexión a la base de datos MySQL usando PDO
// Parámetros: host, nombre BD, charset, usuario y contraseña
$pdo = new PDO(
    'mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4', 
    'root', 
    'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc'
);

// Configura PDO para que lance excepciones en errores SQL
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtiene el parámetro 'email' de la URL (GET)
// Si no existe, asigna una cadena vacía
$email = $_GET['email'] ?? '';

// Valida si el email está vacío
if (!$email) {
    // Devuelve error en formato JSON si falta el email
    echo json_encode([
        "success" => false, 
        "error" => "Falta el parámetro 'email'"
    ]);
    exit; // Termina la ejecución del script
}

// Prepara la consulta SQL para obtener los intentos del usuario
// usando un parámetro preparado (?)
$stmt = $pdo->prepare("SELECT intentos FROM usuarios WHERE email = ?");

// Ejecuta la consulta con el email proporcionado
$stmt->execute([$email]);

// Obtiene el resultado como array asociativo
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica si se encontró el usuario
if ($result) {
    // Devuelve éxito con los intentos (convertidos a entero)
    echo json_encode([
        "success" => true, 
        "intentos" => (int)$result['intentos']
    ]);
} else {
    // Devuelve error si el usuario no existe
    echo json_encode([
        "success" => false, 
        "error" => "Usuario no encontrado"
    ]);
}
