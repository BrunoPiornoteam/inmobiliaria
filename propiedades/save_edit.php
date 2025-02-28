<?php
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    $titulo = $_POST['titulo'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $ubicacion = $_POST['ubicacion'] ?? '';
    $tamano = $_POST['tamano'] ?? 0;
    $dormitorios = $_POST['dormitorios'] ?? 0;
    $banos = $_POST['banos'] ?? 0;
    $caracteristicas = $_POST['caracteristicas'] ?? '';

    $precio = is_numeric($precio) ? $precio : 0;
    $tamano = is_numeric($tamano) ? $tamano : 0;
    $dormitorios = is_numeric($dormitorios) ? $dormitorios : 0;
    $banos = is_numeric($banos) ? $banos : 0;

    $imagenes = [];
    if (isset($_FILES['imagenes']) && count($_FILES['imagenes']['name']) > 0) {
        $uploads_dir = '../uploads/';
        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
            // Verificar que el archivo tmp_name no esté vacío
            if (!empty($tmp_name)) {
                $file_name = basename($_FILES['imagenes']['name'][$key]);
                $file_path = $uploads_dir . $file_name;

                // Verificar si el archivo es una imagen válida
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $file_type = mime_content_type($tmp_name);

                if (in_array($file_type, $allowed_types)) {
                    // Verificar si la imagen se sube correctamente
                    if (move_uploaded_file($tmp_name, $file_path)) {
                        $imagenes[] = $file_name;
                    }
                }
            }
        }
    }

    $imagenes_str = implode(',', $imagenes);

    $stmt = $pdo->prepare("UPDATE propiedades SET titulo = ?, precio = ?, ubicacion = ?, tamano = ?, dormitorios = ?, banos = ?, caracteristicas = ?, imagenes = ? WHERE id = ?");
    $stmt->execute([$titulo, $precio, $ubicacion, $tamano, $dormitorios, $banos, $caracteristicas, $imagenes_str, $id]);

    header("Location: edit.php?id=$id&success=true");

    exit;
}
?>
