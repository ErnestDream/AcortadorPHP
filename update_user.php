<?php
// Configuración de cabeceras HTTP para API REST
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

try {
    //Conexion a la BD mediante PDO
    $pdo = new PDO('mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4',
                   'root',
                   'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc');
    
    // Configura PDO para lanzar excepciones en errores SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtiene y decodifica el cuerpo JSON de la petición    
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Validación: verifica que el email esté presente
    if (empty($data['email'])) {
        echo json_encode(["success" => false, "error" => "Falta el email"]);
        exit;
    }

    $email = $data['email']; // Asigna el email recibido

    // Establece tipo = 'Premium' e intentos = -1 (representando "ilimitados")
    $stmt = $pdo->prepare("UPDATE usuarios SET tipo = 'Premium', intentos = -1 WHERE email = ?");
    $stmt->execute([$email]);

    /**
     * Evalúa el resultado de la operación
     * - rowCount() devuelve el número de filas afectadas
     */
    if ($stmt->rowCount() > 0) {
        // Éxito: usuario actualizado
        echo json_encode(["success" => true, "message" => "Usuario actualizado a Premium con intentos ilimitados"]);
    } else {
        // Falla: no se actualizó ningún usuario (email no existe)
        echo json_encode(["success" => false, "message" => "No se actualizó el usuario"]);
    }

} catch (PDOException $e) {
    // Error específico de la base de datos
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
