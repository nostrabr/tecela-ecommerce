<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    require_once __DIR__ . '/../../../../bd/conecta.php';

    $sql = "SELECT * FROM vagas ORDER BY data_criacao DESC";
    $result = $conn->query($sql);

    $vagas = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Decodifica o JSON dos requisitos
            $requisitos_array = json_decode($row['requisitos'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $requisitos_array = [$row['requisitos']]; // Fallback se não for JSON válido
            }
            $row['requisitos_formatados'] = $requisitos_array;
            $vagas[] = $row;
        }
    }

    echo json_encode($vagas, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao carregar vagas: ' . $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>