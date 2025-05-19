<?php
include 'conexion.php';

$data = json_decode(file_get_contents("php://input"));
$email = $data->email;

$sql = "SELECT urls.slug, urls.url_original, urls.fecha_creacion 
        FROM urls 
        JOIN usuarios ON urls.usuario_id = usuarios.id 
        WHERE usuarios.email = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$response = [];
while ($row = $result->fetch_assoc()) {
    $response[] = $row;
}

echo json_encode(["success" => true, "urls" => $response]);
?>
