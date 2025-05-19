<?php

//Conectamos a la base de datos utilizando PDO
$pdo = new PDO(
	'mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4',
      	'root',
	'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc'
);

//Habilitamos el modo error del PDO para que lance excepciones en lugar de fallos
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//Obtención del slug de la URL solicitda 
$path = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/");

//Si no se proporciona un SLUG, mostramos un mensjae de bienvenida, lo mismo sucede si se accede a index.php
if ($path === "" || $path === "index.php") {
    echo "Bienvenido. Usa una URL corta para redirigir.";
    exit;
}

//Busqueda en la BD la URL original asociada al slug
$stmt = $pdo->prepare("SELECT url FROM urls WHERE slug = ?");
$stmt->execute([$path]);

//Si se encuentra la URL original, redirigimos al usuario
if ($row = $stmt->fetch()) {
    header("Location: " . $row['url']); // ->Redicción HTTP a la URL original
    exit;
} else {
    //Si no se encuentra en la BD, se devuelve un 404
    http_response_code(404);
    echo "404 - URL no encontrada";
}
?>
