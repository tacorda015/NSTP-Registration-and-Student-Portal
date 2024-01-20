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
$user_query = "SELECT t.full_name, t.component_name, g.group_name FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE user_account_id = $user_account_id";
$user_result = $con->query($user_query);
$user_row = $user_result->fetch_assoc();

$group_id = $_GET['group_id'];
$group_query = "SELECT t.*, g.group_name FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE t.group_id = $group_id AND role_account_id = 2";
$group_result = $con->query($group_query);
// $group_row = $group_result->fetch_assoc();
$header = $user_row['group_name'] . ' Student List';


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
$columnl = 'l';
$midpointColumn = chr(ord($columnA) + (ord($columnl) - ord($columnA)) / 6);
$logo->setCoordinates($midpointColumn . '2'); // Position of the logo
// Set the size of the image (adjust these values as needed)
$logo->setWidth(160); // Set the width in pixels
$logo->setHeight(100); // Set the height in pixels
// Merge the cells to center the logo horizontally
$sheet->mergeCells($columnA . '2:' . $columnl . '2');
$logo->setOffsetX(-205); // Set the padding/margin to the right (adjust the value as needed)
$logo->setOffsetY(-8); // Set the padding/margin to the right (adjust the value as needed)
$logo->setWorksheet($sheet);
$sheet->getStyle($midpointColumn . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);



// Set the column headers
$sheet->setCellValue('A2', 'Republic of the Philippines');
$sheet->mergeCells('A2:l2');
$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(12);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A3', 'CAVITE STATE UNIVERSITY');
$sheet->mergeCells('A3:l3');
$sheet->getStyle('A3')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A3")->getFont()->setSize(12);
$sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A4', 'CCAT Campus');
$sheet->mergeCells('A4:l4');
$objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(12);
$sheet->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A5', 'Rosario, Cavite');
$sheet->mergeCells('A5:l5');
$objPHPExcel->getActiveSheet()->getStyle("A5")->getFont()->setSize(12);
$sheet->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A7', 'STUDENT LIST');
$sheet->mergeCells('A7:l7');
$sheet->getStyle('A7')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A7")->getFont()->setSize(12);
$sheet->getStyle('A7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A9', 'Incharge Person: ');
$sheet->getStyle('A9')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A9")->getFont()->setSize(12);
$sheet->setCellValue('B9', $user_row['full_name']);
$sheet->mergeCells('B9:l9');
$sheet->getStyle('B9:l9')->applyFromArray($underlineStyle);
$objPHPExcel->getActiveSheet()->getStyle("B9")->getFont()->setSize(12);

$sheet->setCellValue('A10', 'Group Name: ');
$sheet->getStyle('A10')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A10")->getFont()->setSize(12);
$sheet->setCellValue('B10', $user_row['group_name']);
$sheet->mergeCells('B10:l10');
$sheet->getStyle('B10:l10')->applyFromArray($underlineStyle);
$objPHPExcel->getActiveSheet()->getStyle("B10")->getFont()->setSize(12);

$sheet->setCellValue('A11', 'Component: ');
$sheet->getStyle('A11')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A11")->getFont()->setSize(12);
$sheet->setCellValue('B11', $user_row['component_name']);
$sheet->mergeCells('B11:l11');
$sheet->getStyle('B11:l11')->applyFromArray($underlineStyle);
$objPHPExcel->getActiveSheet()->getStyle("B11")->getFont()->setSize(12);

$sheet->mergeCells('A6:l6');
$sheet->mergeCells('A8:l8');
$objPHPExcel->getActiveSheet()->getStyle("A13:l13")->getFont()->setSize(12);

$sheet->setCellValue('A13', 'Serial Number');
// $sheet->getStyle('A13')->getFont()->setBold(true)->setSize(12);
$sheet->mergeCells('A13:A14');
$sheet->setCellValue('B13', 'Student Name');
$sheet->mergeCells('B13:D13');
$sheet->setCellValue('B14', 'Surname');
$sheet->setCellValue('C14', 'First Name');
$sheet->setCellValue('D14', 'Middle Initail');
$sheet->setCellValue('E13', 'Course');
$sheet->mergeCells('E13:E14');
$sheet->setCellValue('F13', 'Sex');
$sheet->mergeCells('F13:F14');
$sheet->setCellValue('G13', 'Birtday');
$sheet->setCellValue('G14', '(MM/DD/YYYY)');
$sheet->setCellValue('H13', 'Address');
$sheet->mergeCells('H13:J13');
$sheet->setCellValue('H14', 'Street/Brgy.');
$sheet->setCellValue('I14', 'City/Municipality');
$sheet->setCellValue('J14', 'Province');
$sheet->setCellValue('K13', 'Email Address');
$sheet->mergeCells('K13:K14');
$sheet->setCellValue('L13', 'Telephone/CP Number');
$sheet->mergeCells('L13:L14');

$sheet->getStyle('A13:L13')->applyFromArray($headerStyle);

// Adjust column widths automatically for header
foreach (range('A', 'L') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

if ($group_result  && $group_result->num_rows > 0) {
    $rowCounter = 15; // Start populating data from the third row
    while ($row = $group_result->fetch_assoc()) {
        
        // Populate the rows with studentlist data
        $sheet->setCellValue('A' . $rowCounter, $row['serialNumber'] ?? '');
        $sheet->setCellValue('B' . $rowCounter, $row['surname'] ?: 'No Data');
        $sheet->setCellValue('C' . $rowCounter, $row['firstname'] ?: 'No Data');
        $sheet->setCellValue('D' . $rowCounter, $row['middlename'] ?: 'No Data');
        $sheet->setCellValue('E' . $rowCounter, $row['course'] ?: 'No Data');
        $sheet->setCellValue('F' . $rowCounter, $row['gender'] ?: 'No Data');
        $sheet->setCellValue('G' . $rowCounter, $row['birthday'] ?: 'No Data');
        $sheet->setCellValue('H' . $rowCounter, $row['baranggay'] ?: 'No Data');
        $sheet->setCellValue('I' . $rowCounter, $row['city'] ?: 'No Data');
        $sheet->setCellValue('J' . $rowCounter, $row['province'] ?: 'No Data');
        $sheet->setCellValue('K' . $rowCounter, $row['email_address'] ?: 'No Data');
        $sheet->setCellValueExplicit('L' . $rowCounter, $row['contactNumber'] ?: 'No Data', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);


        $rowCounter++;

        // foreach (range('A', 'L') as $column) {
        //     $sheet->getColumnDimension($column)->setAutoSize(true);
        // }
        // Set the borders for the entire range of cells
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A13:L' . ($rowCounter - 1))->applyFromArray($borderStyle);
        
        // Set the alignment for the entire range of cells
        $alignmentStyle = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('B'. ($rowCounter - 1).':L' . ($rowCounter - 1))->applyFromArray($alignmentStyle);
        
    }
} else {
    $sheet->setCellValue('A13', 'No Student data found');
    $sheet->getStyle('A13')->getFont()->setBold(true)->setSize(14);
    $sheet->mergeCells('A13:L13');
    $sheet->getStyle('A13')->applyFromArray([
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);

     // Set the borders for the entire range of cells
     $borderStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ];
    $sheet->getStyle('A1:L6')->applyFromArray($borderStyle);
}

// Set the file name and mime type for the XLSX file
$filename = $header . '.xlsx';
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
