<?php

// Se establece el encabezado de respuesta para indicar que se devolverá un JSON
header('Content-Type: application/json');

// Verifica que se haya pasado el parámetro 'email'
if (!isset($_GET['email'])) {
    echo json_encode(['error' => 'Falta el parámetro email']);
    exit;
}

$email = $_GET['email'];

try {
    // Conexión a la BD de MySQL mediante PDO
    $pdo = new PDO(
        'mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4',
        'root',
        'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc'
    );

    // Configuración del modo error para que arroje una excepción en caso de fallar
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener historial por email
    $stmt = $pdo->prepare("SELECT slug, url FROM urlsPrueba WHERE email = ?");
    $stmt->execute([$email]);

    // Resultado en formato JSON
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    // Error en la base de datos
    echo json_encode([
        'error' => 'Error en la base de datos',
        'message' => $e->getMessage()
    ]);
}
