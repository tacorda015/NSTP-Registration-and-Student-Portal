<?php
ob_start();
session_start();
include('../connection.php');
$con = connection();
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// Check if the user ID is provided in the query string
if (!isset($_GET['user_id'])) {
    // User ID is not provided, redirect or handle the error
    header('Location: error.php');
    exit;
}

$user_id = $_GET['user_id'];

$student_info = "SELECT * FROM useraccount WHERE user_account_id = $user_id";
$student_info_result = $con->query($student_info);
$student_info_data = $student_info_result->fetch_assoc();
$student_department = strtoupper($student_info_data['course']); // Convert to uppercase

$attendance_query = "SELECT * FROM attendancetable WHERE user_account_id = $user_id";
$attendance_result = $con->query($attendance_query);

// Create a new Spreadsheet object
$objPHPExcel = new Spreadsheet();

// Set the active sheet
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();

// Add the logo image
$logoPath = '../assets/img/Logo.png'; // Path to the logo image
$logo = new Drawing();
$logo->setName('Logo');
$logo->setDescription('Company Logo');
$logo->setPath($logoPath);
// Calculate the midpoint between columns A and E
$columnA = 'A';
$columnE = 'E';
$midpointColumn = chr(ord($columnA) + (ord($columnE) - ord($columnA)) / 2);
$logo->setCoordinates($midpointColumn . '2'); // Position of the logo
// Set the size of the image (adjust these values as needed)
$logo->setWidth(90); // Set the width in pixels
$logo->setHeight(60); // Set the height in pixels
// Merge the cells to center the logo horizontally
$sheet->mergeCells($columnA . '2:' . $columnE . '2');
$logo->setOffsetX(-115); // Set the padding/margin to the right (adjust the value as needed)
$logo->setOffsetY(50); // Set the padding/margin to the right (adjust the value as needed)
$logo->setWorksheet($sheet);


// Set the column headers
$sheet->setCellValue('A2', 'Republic of the Philippines');
$sheet->mergeCells('A2:E2');
$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(12);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A3', 'CAVITE STATE UNIVERSITY');
$sheet->mergeCells('A3:E3');
$sheet->getStyle('A3')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A3")->getFont()->setSize(12);
$sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A4', 'CCAT Campus');
$sheet->mergeCells('A4:E4');
$objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(12);
$sheet->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A5', 'Rosario, Cavite');
$sheet->mergeCells('A5:E5');
$objPHPExcel->getActiveSheet()->getStyle("A5")->getFont()->setSize(12);
$sheet->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A7', 'ATTENDACE SHEET');
$sheet->mergeCells('A7:E7');
$sheet->getStyle('A7')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A6")->getFont()->setSize(12);
$sheet->getStyle('A7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A9', $student_department);
$sheet->mergeCells('A9:E9');
$sheet->getStyle('A9')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A7")->getFont()->setSize(12);
$sheet->getStyle('A9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


$sheet->mergeCells('A6:E6');
$sheet->mergeCells('A8:E8');
$sheet->mergeCells('A10:E10');
$objPHPExcel->getActiveSheet()->getStyle("A11:E11")->getFont()->setSize(12);

$sheet->setCellValue('A11', 'Date');
$sheet->setCellValue('B11', 'Status');
$sheet->setCellValue('C11', 'Time-in');
$sheet->setCellValue('D11', 'Time-out');
$sheet->setCellValue('E11', 'Remark');

// ...

if ($attendance_result && $attendance_result->num_rows > 0) {
    $rowCounter = 12; // Start populating data from the third row
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
        $sheet->getStyle('A11:E' . ($rowCounter - 1))->applyFromArray($borderStyle);
        
        // Set the alignment for the entire range of cells
        $alignmentStyle = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:E' . ($rowCounter - 1))->applyFromArray($alignmentStyle);

        $sheet->getStyle('A'. ($rowCounter - 1).':E' . ($rowCounter - 1))->getFont()->setSize(12);
        
    }
} else {
    $sheet->mergeCells('A12:E12');
    $borderStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    $alignmentStyle = [
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ];
    $sheet->getStyle('A12')->applyFromArray($alignmentStyle);
    $sheet->getStyle('A11:E12')->applyFromArray($borderStyle);

    $sheet->setCellValue('A12', 'No attendance data found');
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
