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
if (!isset($_GET['download'])) {
    // User ID is not provided, redirect or handle the error
    header('Location: studentlist.php');
    exit;
}elseif(isset($_GET['group_id']) && isset($_GET['download'])){
    $groupview_id = $_GET['group_id'];
    $groupName = $_GET['group_name'];
    $schoolyear_id = $_GET['schoolyear_id'];

    $schoolyear_query = "SELECT * FROM schoolyeartable WHERE schoolyear_id = $schoolyear_id";
    $schoolyear_result = $con->query($schoolyear_query);
    $schoolyear_data = mysqli_fetch_assoc($schoolyear_result);

    $schoolyear_start = $schoolyear_data['schoolyear_start'];
    $schoolyear_end = $schoolyear_data['schoolyear_end'];
    $semester_id = $schoolyear_data['semester_id'];

    $semesterText = ($semester_id == 1) ? 'First Semester' : 'Second Semester';
    $current_schoolyear = $schoolyear_start . '-' . $schoolyear_end;
    
    $group_query = "SELECT t.*, g.group_name FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE role_account_id = 2 AND t.group_id = $groupview_id AND g.group_name = '$groupName'";
    $group_result = $con->query($group_query);
    $header = $groupName;
}else{
    $downloadlist = $_GET['download'];
    $schoolyear_id = $_GET['schoolyear_id'];

    $schoolyear_query = "SELECT * FROM schoolyeartable WHERE schoolyear_id = $schoolyear_id";
    $schoolyear_result = $con->query($schoolyear_query);
    $schoolyear_data = mysqli_fetch_assoc($schoolyear_result);

    $schoolyear_start = $schoolyear_data['schoolyear_start'];
    $schoolyear_end = $schoolyear_data['schoolyear_end'];
    $semester_id = $schoolyear_data['semester_id'];

    $semesterText = ($semester_id == 1) ? 'First Semester' : 'Second Semester';
    $current_schoolyear = $schoolyear_start . '-' . $schoolyear_end;

    if($downloadlist === 'all'){
        $group_query = "SELECT t.*, g.group_name FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE role_account_id = 2 AND t.schoolyear_id = $schoolyear_id AND t.semester_id = $semester_id";
        $group_result = $con->query($group_query);
        $header = 'Student List';
    }else if($downloadlist === 'CWTS'){
        $group_query = "SELECT t.*, g.group_name FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE role_account_id = 2 AND component_name = '$downloadlist' AND t.schoolyear_id = $schoolyear_id AND t.semester_id = $semester_id";
        $group_result = $con->query($group_query);
        $header = 'CWTS Student List';
    }else if($downloadlist === 'ROTC'){
        $group_query = "SELECT t.*, g.group_name FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE role_account_id = 2 AND component_name = '$downloadlist' AND t.schoolyear_id = $schoolyear_id AND t.semester_id = $semester_id";
        $group_result = $con->query($group_query);
        $header = 'ROTC Student List';
    }else if($downloadlist === 'active'){
        $group_query = "SELECT t.*, g.group_name FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE role_account_id = 2 AND user_status = '$downloadlist' AND t.schoolyear_id = $schoolyear_id AND t.semester_id = $semester_id";
        $group_result = $con->query($group_query);
        $header = 'Active Student List';
    }else if($downloadlist === 'disabled'){
        $group_query = "SELECT t.*, g.group_name FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE role_account_id = 2 AND user_status = '$downloadlist' AND t.schoolyear_id = $schoolyear_id AND t.semester_id = $semester_id";
        $group_result = $con->query($group_query);
        $header = 'Disabled Student List';
    }
}

// $group_query = "SELECT t.*, g.group_name FROM useraccount t LEFT JOIN grouptable g ON t.group_id = g.group_id WHERE role_account_id = 2";
// $group_result = $con->query($group_query);
// $group_row = $group_result->fetch_assoc();


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
$alignmentLeft = [
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
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
$columnL = 'L';
$midpointColumn = chr(ord($columnA) + (ord($columnL) - ord($columnA)) / 2);
$logo->setCoordinates($midpointColumn . '2'); // Position of the logo
// Set the size of the image (adjust these values as needed)
$logo->setWidth(160); // Set the width in pixels
$logo->setHeight(100); // Set the height in pixels
// Merge the cells to center the logo horizontally
$sheet->mergeCells($columnA . '2:' . $columnL . '2');
$logo->setOffsetX(-15); // Set the padding/margin to the right (adjust the value as needed)
$logo->setOffsetY(-8); // Set the padding/margin to the right (adjust the value as needed)
$logo->setWorksheet($sheet);
$sheet->getStyle($midpointColumn . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

// Set the column headers
$sheet->setCellValue('A2', 'Republic of the Philippines');
$sheet->mergeCells('A2:L2');
$objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setSize(12);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A3', 'CAVITE STATE UNIVERSITY');
$sheet->mergeCells('A3:L3');
$sheet->getStyle('A3')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A3")->getFont()->setSize(12);
$sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A4', 'CCAT Campus');
$sheet->mergeCells('A4:L4');
$objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(12);
$sheet->getStyle('A4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A5', 'Rosario, Cavite');
$sheet->mergeCells('A5:L5');
$objPHPExcel->getActiveSheet()->getStyle("A5")->getFont()->setSize(12);
$sheet->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A7', "$header");
$sheet->setTitle($header);
$sheet->mergeCells('A7:L7');
$sheet->getStyle('A7')->getFont()->setBold(true); // Set bold font
$objPHPExcel->getActiveSheet()->getStyle("A7")->getFont()->setSize(12);
$sheet->getStyle('A7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A9', 'Serial Number');
$sheet->mergeCells('A9:A10');
$sheet->setCellValue('B9', 'Student Name');
$sheet->mergeCells('B9:D9');
$sheet->setCellValue('B10', 'Surname');
$sheet->setCellValue('C10', 'First Name');
$sheet->setCellValue('D10', 'Middle Initail');
$sheet->setCellValue('E9', 'Course');
$sheet->mergeCells('E9:E10');
$sheet->setCellValue('F9', 'Sex');
$sheet->mergeCells('F9:F10');
$sheet->setCellValue('G9', 'Birtday');
$sheet->setCellValue('G10', '(MM/DD/YYYY)');
$sheet->setCellValue('H9', 'Address');
$sheet->mergeCells('H9:J9');
$sheet->setCellValue('H10', 'Street/Brgy.');
$sheet->setCellValue('I10', 'City/Municipality');
$sheet->setCellValue('J10', 'Province');
$sheet->setCellValue('K9', 'Email Address');
$sheet->mergeCells('K9:K10');
$sheet->setCellValue('L9', 'Telephone/CP Number');
$sheet->mergeCells('L9:L10');

$alignment = $sheet->getStyle('A9:L10')->getAlignment();
$alignment->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$alignment->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);


$objPHPExcel->getActiveSheet()->getStyle("A9:L9")->getFont()->setSize(12);
$sheet->getStyle('A2:L2')->applyFromArray($headerStyle);

// Adjust column widths automatically for header
foreach (range('A', 'L') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

if ($group_result  && $group_result->num_rows > 0) {
    $rowCounter = 11; // Start populating data from the third row
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
        $sheet->getStyle('A9:L' . ($rowCounter - 1))->applyFromArray($borderStyle);
        
        // Set the alignment for the entire range of cells
        $alignmentStyle = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('B'. ($rowCounter - 1).':L' . ($rowCounter - 1))->applyFromArray($alignmentStyle);
        
        $sheet->getStyle('B'. ($rowCounter - 1).':L' . ($rowCounter - 1))->getFont()->setSize(12);
    }
} else {
    $sheet->setCellValue('A11', 'No Student data found');
    $sheet->getStyle('A11')->getFont()->setBold(true)->setSize(14);
    $sheet->mergeCells('A11:L11');
    $sheet->getStyle('A11')->applyFromArray([
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
    $sheet->getStyle('A9:L11')->applyFromArray($borderStyle);
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
