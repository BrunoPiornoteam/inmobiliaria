<?php
require_once('../includes/db.php');

// Desactivar la salida de errores HTML
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json');

try {
    // Primero verificar si la tabla existe
    $check_table = $pdo->query("SHOW TABLES LIKE 'facturas'");
    if ($check_table->rowCount() == 0) {
        throw new Exception("La tabla 'facturas' no existe");
    }

    // Verificar la estructura de la tabla
    $columns = $pdo->query("SHOW COLUMNS FROM facturas")->fetchAll(PDO::FETCH_COLUMN);
    $required_columns = ['id', 'cliente_id', 'fecha_emision', 'concepto', 'monto', 'estado'];
    $missing_columns = array_diff($required_columns, $columns);
    
    if (!empty($missing_columns)) {
        throw new Exception("Faltan las siguientes columnas en la tabla facturas: " . implode(", ", $missing_columns));
    }

    // Obtener parámetros de filtro
    $fecha_desde = filter_input(INPUT_POST, 'fecha_desde', FILTER_SANITIZE_STRING);
    $fecha_hasta = filter_input(INPUT_POST, 'fecha_hasta', FILTER_SANITIZE_STRING);
    $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
    $cliente = filter_input(INPUT_POST, 'cliente', FILTER_SANITIZE_STRING);

    // Construir la consulta base
    $query = "
        SELECT 
            f.id,
            f.fecha_emision,
            f.concepto,
            f.monto,
            f.estado,
            c.nombre as cliente_nombre,
            c.email as cliente_email
        FROM facturas f
        JOIN clientes c ON f.cliente_id = c.id
        WHERE 1=1";

    $params = [];

    // Agregar filtros si están presentes
    if ($fecha_desde) {
        $query .= " AND f.fecha_emision >= ?";
        $params[] = $fecha_desde;
    }

    if ($fecha_hasta) {
        $query .= " AND f.fecha_emision <= ?";
        $params[] = $fecha_hasta;
    }

    if ($estado) {
        $query .= " AND f.estado = ?";
        $params[] = $estado;
    }

    if ($cliente) {
        $query .= " AND (c.nombre LIKE ? OR c.email LIKE ?)";
        $params[] = "%$cliente%";
        $params[] = "%$cliente%";
    }

    $query .= " ORDER BY f.fecha_emision DESC";

    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($facturas);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
