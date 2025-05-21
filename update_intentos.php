<?php

// Configuración de cabeceras HTTP para API REST
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Conexión a la BD por medio de PDO
$pdo = new PDO('mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4',
               'root',
               'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc');

// Configura PDO para lanzar excepciones en errores SQL
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtiene los datos del cuerpo de la petición POST en formato JSON
$data = json_decode(file_get_contents("php://input"), true);
// Extrae el email del array de datos, con valor por defecto vacío
$email = $data["email"] ?? '';

// Validación básica: verifica que se haya proporcionado un email
if (!$email) {
    echo json_encode(["success" => false, "error" => "Falta el email"]);
    exit;
}

/**
 * Prepara y ejecuta la consulta SQL para decrementar los intentos
 * - Solo actualiza si el usuario tiene intentos > 0
 * - Usa parámetros preparados para seguridad
 */
$stmt = $pdo->prepare("UPDATE usuarios SET intentos = intentos - 1 WHERE email = ? AND intentos > 0");
$stmt->execute([$email]);

/**
 * Verifica si se actualizó algún registro
 * - rowCount() devuelve el número de filas afectadas
 */
if ($stmt->rowCount() > 0) {
    // Éxito: se decrementó el contador
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "No se pudo actualizar (quizás sin intentos)"]);
}
