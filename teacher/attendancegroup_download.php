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
if (!isset($_GET['group_id'])) {
    // User ID is not provided, redirect or handle the error
    header('Location: error.php');
    exit;
}
$user_data = $_SESSION['user_data'];
$user_account_id = $user_data['user_account_id'];
$user_query = "SELECT full_name, component_name FROM useraccount WHERE user_account_id = $user_account_id";
$user_result = $con->query($user_query);
$user_row = $user_result->fetch_assoc();

$group_id = $_GET['group_id'];
$specificData = $_GET['specificData'];
$group_query = "SELECT group_name FROM grouptable WHERE group_id = '$group_id'";
$group_result = $con->query($group_query);
$group_row = $group_result->fetch_assoc();




$attendance_query = "SELECT at.*, rs.remarkstatus_name 
                    FROM attendancetable at 
                    LEFT JOIN remarkstatustable rs ON at.remark_status = rs.remarkstatus_id 
                    WHERE at.group_id = $group_id AND at.activity_date = '$specificData'
                    ORDER BY at.attendance_id DESC";
$attendance_result = $con->query($attendance_query);

// Create a new Spreadsheet object
$objPHPExcel = new Spreadsheet();

// Set the active sheet
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();

$underlineStyle = [
    'borders' => [
        'bottom' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
        ],
    ],
];
$headerStyle = [
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
];

// Add the logo image
$logoPath = '../assets/img/Logo.png'; // Path to the logo image
$logo = new Drawing();
$logo->setName('Logo');
$logo->setDescription('Company Logo');
$logo->setPath($logoPath);
// Calculate the midpoint between columns A and E
$columnA = 'A';
$column = 'G';
$midpointColumn = chr(ord($columnA) + (ord($column) - ord($columnA)) / 2);
$logo->setCoordinates($midpointColumn . '2'); // Position of the logo
// Set the size of the image (adjust these values as needed)
$logo->setWidth(160); // Set the width in pixels
$logo->setHeight(100); // Set the height in pixels
// Merge the cells to center the logo horizontally
$sheet->mergeCells($columnA . '2:' . $column . '2');
$logo->setOffsetX(-210); // Set the padding/margin to the right (adjust the value as needed)
$logo->setOffsetY(-8); // Set the padding/margin to the right (adjust the value as needed)
$logo->setWorksheet($sheet);
$sheet->getStyle($midpointColumn . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);



// Set the column headers
$sheet->setCellValue('A2', 'Republic of the Philippines');
$sheet->mergeCells('A2:G2');
$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(12);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A3', 'CAVITE STATE UNIVERSITY');
$sheet->mergeCells('A3:G3');
$sheet->getStyle('A3')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A3")->getFont()->setSize(12);
$sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A4', 'CCAT Campus');
$sheet->mergeCells('A4:G4');
$objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(12);
$sheet->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A5', 'Rosario, Cavite');
$sheet->mergeCells('A5:G5');
$objPHPExcel->getActiveSheet()->getStyle("A5")->getFont()->setSize(12);
$sheet->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A7', 'ATTENDACE SHEET');
$sheet->mergeCells('A7:G7');
$sheet->getStyle('A7')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A7")->getFont()->setSize(12);
$sheet->getStyle('A7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A9', 'Incharge Person: ');
$sheet->getStyle('A9')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A9")->getFont()->setSize(12);
$sheet->setCellValue('B9', $user_row['full_name']);
$sheet->mergeCells('B9:G9');
$sheet->getStyle('B9:G9')->applyFromArray($underlineStyle);
$objPHPExcel->getActiveSheet()->getStyle("B9")->getFont()->setSize(12);

$sheet->setCellValue('A10', 'Group Name: ');
$sheet->getStyle('A10')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A10")->getFont()->setSize(12);
$sheet->setCellValue('B10', $group_row['group_name']);
$sheet->mergeCells('B10:G10');
$sheet->getStyle('B10:G10')->applyFromArray($underlineStyle);
$objPHPExcel->getActiveSheet()->getStyle("B10")->getFont()->setSize(12);

$sheet->setCellValue('A11', 'Component: ');
$sheet->getStyle('A11')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A11")->getFont()->setSize(12);
$sheet->setCellValue('B11', $user_row['component_name']);
$sheet->mergeCells('B11:G11');
$sheet->getStyle('B11:G11')->applyFromArray($underlineStyle);
$objPHPExcel->getActiveSheet()->getStyle("B11")->getFont()->setSize(12);

$sheet->mergeCells('A6:G6');
$sheet->mergeCells('A8:G8');
$objPHPExcel->getActiveSheet()->getStyle("A13:G13")->getFont()->setSize(12);

$sheet->setCellValue('A13', 'Student Name');
$sheet->setCellValue('B13', 'Date');
$sheet->setCellValue('C13', 'Status');
$sheet->setCellValue('D13', 'Time-in');
$sheet->setCellValue('E13', 'Time-out');
$sheet->setCellValue('F13', 'Remark');
$sheet->setCellValue('G13', 'Report Status');

// $leftAlignStyle = [
//     'alignment' => [
//         'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
//         'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
//     ],
// ];
// $sheet->getStyle('A2:A3')->applyFromArray($leftAlignStyle);

$sheet->getStyle('A4:G4')->applyFromArray($headerStyle);

$sheet->getStyle('B9:G9')->applyFromArray($underlineStyle);
$sheet->getStyle('B10:G10')->applyFromArray($underlineStyle);
$sheet->getStyle('B11:G11')->applyFromArray($underlineStyle);

if ($attendance_result) {
    $rowCounter = 14; // Start populating data from the third row
    while ($row = $attendance_result->fetch_assoc()) {
        // Populate the rows with attendance data
        $sheet->setCellValue('A' . $rowCounter, $row['student_name']);
        $sheet->setCellValue('B' . $rowCounter, $row['activity_date']);
        $sheet->setCellValue('C' . $rowCounter, $row['attendance_status']);
        $sheet->setCellValue('D' . $rowCounter, ($row['time-in'] === null) ? '--:--' : date('H:i', strtotime($row['time-in'])));
        $sheet->setCellValue('E' . $rowCounter, ($row['time-out'] === null) ? '--:--' : date('H:i', strtotime($row['time-out'])));
        $sheet->setCellValue('F' . $rowCounter, $row['remark']);
        $sheet->setCellValue('G' . $rowCounter, $row['remarkstatus_name']);

        // $dataStyle = [
        //     'alignment' => [
        //         'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        //         'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        //     ],
        // ];
        // $sheet->getStyle('B' . $rowCounter . ':G' . $rowCounter)->applyFromArray($dataStyle);

        $rowCounter++;

        // foreach (range('A', 'G') as $column) {
        //     $sheet->getColumnDimension($column)->setAutoSize(true);
        // }

        foreach (range('A', 'G') as $column) {
            if ($column === 'A') {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            } elseif($column === 'B'){
                $sheet->getColumnDimension($column)->setWidth(12); // Adjust the width as needed
            } elseif($column === 'C'){
                $sheet->getColumnDimension($column)->setWidth(10); // Adjust the width as needed
            }elseif($column === 'D' || $column === 'E' || $column === 'F'){
                $sheet->getColumnDimension($column)->setWidth(8); // Adjust the width as needed
            } else {
                $sheet->getColumnDimension($column)->setWidth(15); // Adjust the width as needed
            }
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
        $sheet->getStyle('A13:G' . ($rowCounter - 1))->applyFromArray($borderStyle);
        
        // Set the alignment for the entire range of cells
        $alignmentStyle = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('B'. ($rowCounter - 1).':G' . ($rowCounter - 1))->applyFromArray($alignmentStyle);

        $sheet->getStyle('A'. ($rowCounter - 1).':G' . ($rowCounter - 1))->getFont()->setSize(10);
        
    }
} else {
    // No attendance data found
    $sheet->setCellValue('A5', 'No attendance data found');
}
// $columnWidth = 25; // Desired column width in Excel units

// foreach (range('A', 'G') as $column) {
//     $sheet->getColumnDimension($column)->setWidth($columnWidth);
// }

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
