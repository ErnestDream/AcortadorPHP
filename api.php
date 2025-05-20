<?php

//Se establece el encabezado de respuesta para indicar que se devolvverá un JSON
header('Content-Type: application/json');

//Conexión a la BD de MySQL mediante PDO
$pdo = new PDO('mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4',
      'root',
      'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc');

//Configuración del modo error para que arroje una excepción en caso de fallar
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtención del método HTTP para las solicitudes GET, POST
$method = $_SERVER['REQUEST_METHOD'];

//Método POST, para crear una URL acortada

if ($method === 'POST') {
	
    //Lee y decodifica el cuerpo JSON 	
    $data = json_decode(file_get_contents("php://input"), true);


    //Verificación de que se haya enviado una URL valida
    if (!isset($data['url']) || empty($data['url'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Faltan datos.']);
        exit;
    }

    //Verificación de que se haya enviado un IdUsuario válido o existente
    if (!isset($data['idUsuario'])) {
        http_response_code(400);
        echo json_encode(['error' => 'No se proporcionó el idUsuario.']);
        exit;
    }

    //Extracción de los datos del cuerpo de la solicitud
    $url = $data['url'];
    //$idUsuario = $data['idUsuario'];
	$email = $data['email'];

    //Generación de un SLUG único de 6 caracteres a partir de un HASH MD5
    $slug = substr(md5(uniqid(rand(), true)), 0, 6);


    // Insersión de la URL original, del slug y del IdUsuario en la base de datos
    $stmt = $pdo->prepare("INSERT INTO urlsPrueba (slug, url, email) VALUES (?, ?, ?)");
    $stmt->execute([$slug, $url, $email]);

    //Construcción de la URL acortada
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['PHP_SELF']), '/');
    $shortUrl = "http://$host$path/$slug";

    //Devolución de un objeto JSON con la información de la URL acortada
    echo json_encode([
        "slug" => $slug,
        "url" => $url,
        "short_url" => $shortUrl
    ]);


    //Método GET, para obtener la URL original mediante el slug

} elseif ($method === 'GET') {
	
    // Obtiene el parametro slug de la URL, o una candena vacia
    $slug = $_GET['slug'] ?? '';

    //Busqueda de la URL original asociada al slug en la BD
    $stmt = $pdo->prepare("SELECT url FROM urls WHERE slug = ?");
    $stmt->execute([$slug]);
    $resultado = $stmt->fetch();

    
    if ($resultado) {
	//Si se encuentra la URL, se devuelve en formato JSON
        echo json_encode(["slug" => $slug, "url" => $resultado['url']]);
    } else {
	//Si no se encuentra, se devuelve un error 404
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Slug no encontrada.']);
    }

// Método DELETE para la eliminación de una URL por medio de su SLUG

if ($method === 'DELETE') {
    // Leer el cuerpo de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['slug'])) {
        http_response_code(400);
        echo json_encode(['error' => 'No se proporcionó el slug.']);
        exit;
    }

    $slug = $data['slug'];

    $stmt = $pdo->prepare("DELETE FROM urls WHERE slug = ?");
    $stmt->execute([$slug]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['message' => 'URL eliminada con éxito.']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'URL no encontrada.']);
    }
}



// Cualquier otro médodo no permitido

} else {
    http_response_code(405); // Respuesta al método no permitido
    echo json_encode(['error' => 'Método no permitido.']);
}
