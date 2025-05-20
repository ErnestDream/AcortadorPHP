<?php

// Conexion a la BD mediante PDO
$pdo = new PDO(
	'mysql:host=mysql.railway.internal;dbname=railway;charset=utf8mb4',
	'root',
	'PmbYEyrQWIIItorYmqhWMsuaRKHACDcc'
);


//Habilitar el modo de errores para el PDO
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


// Obtenemos los datos enviados por el usuario leyendo el cuerpo del JSON
$datos = json_decode(file_get_contents("php://input"), true);


// Validación de que se haya recibido una URL
if (!isset($datos['url'])) {
    	echo "No enviaste ninguna URL.";
	exit;
    }


// Recibimos la URL que el usuario mandó y la asignamos a una variale
$url = $datos['url'];
$idUsuario = $datos['idUsuario'];


//Generamos un slug único (código corto)
//md5 genera un hash a partir de un valor aleatorio y la hora actial
//substr lo reduce a los primeros 6 caracteres para crea un identificador unico corto
$slug = substr(md5(uniqid(rand(), true)), 0, 6); 


//Guardamos el slug y la URL original en la BD
$stmt = $pdo->prepare("INSERT INTO urls (slug, url, idUsuario) VALUES (?, ?, ?)");
$stmt->execute([$slug, $url, $idUsuario]);


// Construir y ostrar la URL acortada
$dominio = $_SERVER['HTTP_HOST'];
$rutaBase = dirname($_SERVER['PHP_SELF']);


// Asegurarse que no haya barra doble 
$rutaBase = rtrim($rutaBase, '/');


//Mostrar la URL acortada con un enlace
echo "Tu URL corta es: <a href='http://$dominio$rutaBase/$slug'>http://$dominio$rutaBase/$slug</a>";
?>
