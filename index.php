<?php
// 1. Conectamos a la base de datos
$pdo = new PDO('mysql:host=localhost;dbname=urls;charset=utf8mb4', 'root', 'flipper');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


// 2. Obtenemos el slug que está en la URL
$slug = $_GET['slug'] ?? ''; 

// 3. Buscamos en la base de datos
$stmt = $pdo->prepare("SELECT url FROM urls WHERE slug = ?");
$stmt->execute([$slug]);
if ($row = $stmt->fetch()) {
    // 4. Si existe, redirigimos
    header('Location: ' . $row['url']);
    exit;
} else {
    // 5. Si no existe, mostramos error
    echo "URL no encontrada.";
}
?>
