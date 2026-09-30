<?php
session_start();
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/vendor/autoload.php'; // Carga automática de Composer

use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Validar que exista sesión activa
if (!isset($_SESSION['titular'])) {
    header("Location: login.php");
    exit();
}

$titular = $_SESSION['titular'];
$tipoTabla = $_GET['tipo_tabla'] ?? 'retiros'; // 'depositos' o 'retiros'
$filtro = $_GET['filtro'] ?? 'mes';          // 'mes', 'semana', 'año'
$valor = $_GET['valor'] ?? date('m');        // Valor numérico 
$formato = $_GET['formato'] ?? 'pdf';        // 'pdf' o 'excel'

$tablaBD = ($tipoTabla === 'depositos') ? 'depositos_historial' : 'retiros_historial';
$db = Conexion::conectar();

// Construcción dinámica de la consulta SQL según el filtro de fecha
$sql = "SELECT * FROM $tablaBD WHERE titular = ?";
if ($filtro === 'mes') {
    $sql .= " AND MONTH(fecha) = ?";
} elseif ($filtro === 'año') {
    $sql .= " AND YEAR(fecha) = ?";
} elseif ($filtro === 'semana') {
    // Filtra los registros de los últimos 7 días
    $sql .= " AND fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
}
$sql .= " ORDER BY fecha DESC";

$stmt = $db->prepare($sql);

if ($filtro === 'mes' || $filtro === 'anio') {
    $stmt->bind_param("ss", $titular, $valor);
} else {
    $stmt->bind_param("s", $titular);
}

$stmt->execute();
$registros = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


// OPCIÓN A: EXPORTAR A PDF (DOMPDF)

if ($formato === 'pdf') {
    $html = '<h2 style="text-align:center; font-family:sans-serif;">Historial de ' . ucfirst($tipoTabla) . '</h2>';
    $html .= '<p style="font-family:sans-serif;"><strong>Titular:</strong> ' . htmlspecialchars($titular) . '<br><strong>Filtro aplicado:</strong> ' . ucfirst($filtro) . ' (' . $valor . ')</p>';
    $html .= '<table border="1" cellspacing="0" cellpadding="6" width="100%" style="font-family:sans-serif; border-collapse: collapse; font-size: 12px;">';
    $html .= '<tr style="background-color: #f2f2f2;"><th>#ID</th><th>Monto (S/)</th><th>Fecha y Hora</th></tr>';
    
    if (empty($registros)) {
        $html .= '<tr><td colspan="3" align="center">No hay registros para este período.</td></tr>';
    } else {
        foreach ($registros as $row) {
            $html .= '<tr>';
            $html .= '<td>' . $row['id'] . '</td>';
            $html .= '<td>S/ ' . number_format($row['monto'], 2) . '</td>';
            $html .= '<td>' . $row['fecha'] . '</td>';
            $html .= '</tr>';
        }
    }
    $html .= '</table>';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("historial_$tipoTabla.pdf", ["Attachment" => true]);
    exit();
}


// OPCIÓN B: EXPORTAR A EXCEL (PhpSpreadsheet)

elseif ($formato === 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle(ucfirst($tipoTabla));

    // Cabeceras
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Titular');
    $sheet->setCellValue('C1', 'Monto (S/)');
    $sheet->setCellValue('D1', 'Fecha y Hora');

    $rowNum = 2;
    foreach ($registros as $row) {
        $sheet->setCellValue('A' . $rowNum, $row['id']);
        $sheet->setCellValue('B' . $rowNum, $row['titular']);
        $sheet->setCellValue('C' . $rowNum, $row['monto']);
        $sheet->setCellValue('D' . $rowNum, $row['fecha']);
        $rowNum++;
    }

    // Configurar cabeceras HTTP para descarga del archivo Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="historial_' . $tipoTabla . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}