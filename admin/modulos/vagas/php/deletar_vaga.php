<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    require_once __DIR__ . '/../../../../bd/conecta.php';

    if ($_POST) {
        $id = $_POST['id'];
        
        $sql = "DELETE FROM vagas WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Vaga deletada com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao deletar vaga: ' . $conn->error]);
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