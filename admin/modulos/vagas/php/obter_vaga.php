<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    require_once __DIR__ . '/../../../../bd/conecta.php';

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        $sql = "SELECT * FROM vagas WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $vaga = $result->fetch_assoc();
            // Decodifica o JSON dos requisitos e converte para formato de edição
            $requisitos_array = json_decode($vaga['requisitos'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $requisitos_array = [$vaga['requisitos']]; // Fallback se não for JSON válido
            }
            $vaga['requisitos_raw'] = implode(";\n", $requisitos_array);
            echo json_encode($vaga, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Vaga não encontrada']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID não fornecido']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>