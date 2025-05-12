<?php
// 1. Conectamos a la base de datos
	$pdo = new PDO('mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4', 'root', 'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc');
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// 2. Obtenemos el slug que está en la URL
	//$slug = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'); 

//2.5
	//if($slug === '')
	//{
	//	echo "Bienvenido al acortador. Proporciona una URL."
	//	exit;
	//}	


// 3. Buscamos en la base de datos
	//$stmt = $pdo->prepare("SELECT url FROM urls WHERE slug = ?");
	//$stmt->execute([$slug]);
	
	//if ($row = $stmt->fetch()) 
	//{
		
	// 4. Si existe, redirigimos
    	//	header('Location: ' . $row['url']);
	//	exit;

	//} else {
		
	// 5. Si no existe, mostramos error
	http_response_code(404);
    	//echo "URL no encontrada.";
	//}
	//
	$path = trim($_SERVER['REQUEST_URI'], '/');

	// Si no hay slug, muestra una página principal o un formulario
	if ($path === '' || $path === 'index.php') {
    	echo "Bienvenido al acortador. Visita una URL corta para ser redirigido.";
    	exit;
	}

	// Buscar el slug en la base de datos
	$stmt = $pdo->prepare("SELECT url FROM urls WHERE slug = ?");
	$stmt->execute([$path]);

	if ($row = $stmt->fetch()) {
    	// Redirige si se encuentra
    	header("Location: " . $row['url']);
    	exit;
	} else {
    	// Error si no se encuentra
    	http_response_code(404);
    	echo "404 - URL no encontrada";
	}
?>
