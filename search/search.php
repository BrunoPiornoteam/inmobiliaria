<?php session_start();
header('Content-Type: application/json');

error_reporting(E_ALL);

try {
    require_once '../includes/db.php';

    function buscarGeneral($pdo, $termino) {
        $termino = '%' . $termino . '%';
        $resultados = [];

        try {
            if (!$pdo) {
                throw new Exception("Error: No se pudo conectar a la base de datos.");
            }

            // Búsqueda en propiedades
            $query = "SELECT 'propiedad' as tipo, id, titulo, descripcion, precio 
                    FROM propiedades 
                    WHERE titulo LIKE ? OR descripcion LIKE ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$termino, $termino]);
            $resultados = array_merge($resultados, $stmt->fetchAll(PDO::FETCH_ASSOC));

            // Búsqueda en clientes
            $query = "SELECT 'cliente' as tipo, id, nombre, email 
                    FROM clientes 
                    WHERE nombre LIKE ? OR email LIKE ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$termino, $termino]);
            $resultados = array_merge($resultados, $stmt->fetchAll(PDO::FETCH_ASSOC));

            // Búsqueda en contratos
            $query = "SELECT 'contrato' as tipo, id, tipo_contrato, precio, fecha_inicio, fecha_fin, estado 
                    FROM contratos 
                    WHERE tipo_contrato LIKE ? OR estado LIKE ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$termino, $termino]);   
            $resultados = array_merge($resultados, $stmt->fetchAll(PDO::FETCH_ASSOC));

            return $resultados;

        } catch (PDOException $e) {
            throw new Exception("Error en la búsqueda: " . $e->getMessage());
        }
    }

    // Procesar la búsqueda
    if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
        throw new Exception("Parámetro de búsqueda no proporcionado o vacío.");
    }

    $termino = trim($_GET['q']);
    $resultados = buscarGeneral($pdo, $termino);

    echo json_encode($resultados);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
    exit;
}
