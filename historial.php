<?php
header('Content-Type: application/json');

if (!isset($_GET['email'])) {
    echo json_encode(['error' => 'Falta el parámetro email']);
    exit;
}

$email = $_GET['email'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=acortador', 'usuario', 'clave', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("SELECT slug, url FROM urls WHERE email = ?");
    $stmt->execute([$email]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos', 'message' => $e->getMessage()]);
}
?>
