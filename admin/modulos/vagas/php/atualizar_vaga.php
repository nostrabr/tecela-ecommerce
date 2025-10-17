<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    require_once __DIR__ . '/../../../../bd/conecta.php';

    if ($_POST) {
        $id = $_POST['id'];
        $titulo = $_POST['titulo'];
        $requisitos_raw = $_POST['requisitos'];
        
        // Converte os requisitos em array e depois em JSON
        $requisitos_array = array_filter(array_map('trim', explode(';', $requisitos_raw)));
        $requisitos_json = json_encode($requisitos_array, JSON_UNESCAPED_UNICODE);
        
        $sql = "UPDATE vagas SET titulo = ?, requisitos = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $titulo, $requisitos_json, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Vaga atualizada com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar vaga: ' . $conn->error]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>