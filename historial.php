if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['accion']) && $_GET['accion'] === 'historial') {
    require 'conexion.php';

    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'];

    // Obtener el ID del usuario
    $stmtUsuario = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmtUsuario->bind_param("s", $email);
    $stmtUsuario->execute();
    $resultadoUsuario = $stmtUsuario->get_result();

    if ($filaUsuario = $resultadoUsuario->fetch_assoc()) {
        $usuarioId = $filaUsuario['id'];

        // Obtener las URLs del usuario
        $stmtUrls = $conexion->prepare("SELECT url, slug FROM urls WHERE usuario_id = ? ORDER BY fecha_creacion DESC");
        $stmtUrls->bind_param("i", $usuarioId);
        $stmtUrls->execute();
        $resultadoUrls = $stmtUrls->get_result();

        $urls = [];
        while ($fila = $resultadoUrls->fetch_assoc()) {
            $urls[] = $fila;
        }

        echo json_encode([
            "success" => true,
            "urls" => $urls
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "mensaje" => "Usuario no encontrado"
        ]);
    }
}
