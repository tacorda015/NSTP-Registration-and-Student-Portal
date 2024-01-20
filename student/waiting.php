<?php
ob_start();
session_start();
include('../connection.php');
$con = connection();
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Check if the user ID is provided in the query string
if (!isset($_GET['user_id'])) {
    // User ID is not provided, redirect or handle the error
    header('Location: error.php');
    exit;
}

$user_id = $_GET['user_id'];

$attendance_query = "SELECT * FROM attendancetable WHERE user_account_id = $user_id";
$attendance_result = $con->query($attendance_query);

// Create a new Spreadsheet object
$objPHPExcel = new Spreadsheet();

// Set the active sheet
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();

// Set the column headers
$sheet->setCellValue('A1', 'Attendance Record');
$sheet->mergeCells('A1:E1');
$sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A2', 'Date');
$sheet->setCellValue('B2', 'Status');
$sheet->setCellValue('C2', 'Time-in');
$sheet->setCellValue('D2', 'Time-out');
$sheet->setCellValue('E2', 'Remark');

// ...

if ($attendance_result) {
    $rowCounter = 3; // Start populating data from the third row
    while ($row = $attendance_result->fetch_assoc()) {
        // Populate the rows with attendance data
        $sheet->setCellValue('A' . $rowCounter, $row['activity_date']);
        $sheet->setCellValue('B' . $rowCounter, $row['attendance_status']);
        $sheet->setCellValue('C' . $rowCounter, ($row['time-in'] === null) ? '--:--' : date('H:i', strtotime($row['time-in'])));
        $sheet->setCellValue('D' . $rowCounter, ($row['time-out'] === null) ? '--:--' : date('H:i', strtotime($row['time-out'])));
        $sheet->setCellValue('E' . $rowCounter, $row['remark']);

        $rowCounter++;

        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        // Set the borders for the entire range of cells
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A1:E' . ($rowCounter - 1))->applyFromArray($borderStyle);
        
        // Set the alignment for the entire range of cells
        $alignmentStyle = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:E' . ($rowCounter - 1))->applyFromArray($alignmentStyle);
        
    }
} else {
    // No attendance data found
    $sheet->setCellValue('A3', 'No attendance data found');
}

// Set the file name and mime type for the XLSX file
$filename = 'attendance.xlsx';
$mime_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

// Set the appropriate headers to force the browser to download the file
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Create an XLSX writer and save the XLSX file
$objWriter = new Xlsx($objPHPExcel);
$objWriter->save('php://output');

// Stop the script execution to prevent any additional output
exit;
?>
