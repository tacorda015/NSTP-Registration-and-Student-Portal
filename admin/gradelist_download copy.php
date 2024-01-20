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
} else {
    $course_name = $_GET['download'];
    $schoolyear_id = $_GET['schoolyear_id'];

    $schoolyear_query = "SELECT * FROM schoolyeartable WHERE schoolyear_id = $schoolyear_id";
    $schoolyear_result = $con->query($schoolyear_query);
    $schoolyear_data = mysqli_fetch_assoc($schoolyear_result);

    $schoolyear_start = $schoolyear_data['schoolyear_start'];
    $schoolyear_end = $schoolyear_data['schoolyear_end'];
    $semester_id = $schoolyear_data['semester_id'];

    $semesterText = ($semester_id == 1) ? 'First Semester' : 'Second Semester';
    $current_schoolyear = $schoolyear_start . '-' . $schoolyear_end;

    // $grade_query = "SELECT g.student_grade, u.full_name, u.student_number, u.student_section, u.component_name, group_table.group_name FROM gradetable g 
    // LEFT JOIN useraccount u ON g.student_id = u.user_account_id
    // LEFT JOIN grouptable group_table ON g.group_id = group_table.group_id
    // WHERE g.schoolyear_id = $schoolyear_id AND g.semester_id = $semester_id AND u.course = '$course_name'
    // ORDER BY u.student_section ASC";
    $grade_query = "SELECT u.full_name, u.student_number, u.student_section, u.component_name, group_table.group_name, g.student_grade
    FROM useraccount u 
    LEFT JOIN grouptable group_table ON u.group_id = group_table.group_id
    LEFT JOIN gradetable g ON g.student_id = u.user_account_id AND g.schoolyear_id = $schoolyear_id AND g.semester_id = $semester_id
    WHERE u.course = '$course_name'
    ORDER BY u.student_section ASC";



    $grade_result = $con->query($grade_query);

    $header = $course_name;
}

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

// Initialize an array to keep track of worksheet index and row counter for each section
$sectionWorksheets = array();
$objPHPExcel->removeSheetByIndex(0);
if ($grade_result && $grade_result->num_rows > 0) {
    while ($row = $grade_result->fetch_assoc()) {
        $section = $row['student_section'];

        // Check if the worksheet for the section already exists
        if (!isset($sectionWorksheets[$section]) && $row['student_number'] !== null) {

            $getSchedCode = "SELECT s.schedcode_number FROM schedcodetable s 
            LEFT JOIN coursetable c ON s.department_id = c.department_id
            WHERE s.student_section = '$section' AND s.schoolyear_id = $schoolyear_id AND s.semester_id = $semester_id AND s.course = '$course_name'";

            $getSchedCode_result = $con->query($getSchedCode);
            $getSchedCode_data = $getSchedCode_result->fetch_assoc();

            $schedcode_number = $getSchedCode_data['schedcode_number'];
            
            // If not, create a new sheet for the section
            $objPHPExcel->createSheet();
            $sectionIndex = $objPHPExcel->getSheetCount() - 1;
            $objPHPExcel->setActiveSheetIndex($sectionIndex);
            $sheet = $objPHPExcel->getActiveSheet();
            $sheet->setTitle($section);

            // Add the logo image
            $logoPath = '../assets/img/Logo.png'; // Path to the logo image
            $logo = new Drawing();
            $logo->setName('Logo');
            $logo->setDescription('Company Logo');
            $logo->setPath($logoPath);
            // Calculate the midpoint between columns A and E
            $columnA = 'A';
            $columnE = 'E';
            $midpointColumn = chr(ord($columnA) + (ord($columnE) - ord($columnA)) / 3);
            $logo->setCoordinates($midpointColumn . '2'); // Position of the logo
            // Set the size of the image (adjust these values as needed)
            $logo->setWidth(160); // Set the width in pixels
            $logo->setHeight(100); // Set the height in pixels
            // Merge the cells to center the logo horizontally
            $sheet->mergeCells($columnA . '2:' . $columnE . '2');
            // $logo->setOffsetX(-100); // Set the padding/margin to the right (adjust the value as needed)
            $logo->setOffsetY(-8); // Set the padding/margin to the right (adjust the value as needed)
            $logo->setWorksheet($sheet);
            $sheet->getStyle($midpointColumn . '2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

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

            $sheet->setCellValue('A7', "$header");
            $sheet->mergeCells('A7:E7');
            $sheet->getStyle('A7')->getFont()->setBold(true); // Set bold font
            $objPHPExcel->getActiveSheet()->getStyle("A7")->getFont()->setSize(12);
            $sheet->getStyle('A7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A9', 'School Year: ');
            $sheet->getStyle('A9')->getFont()->setBold(true); // Set bold font
            $objPHPExcel->getActiveSheet()->getStyle("A9")->getFont()->setSize(12);
            $sheet->setCellValue('B9', $current_schoolyear);
            $sheet->mergeCells('B9:E9');
            $sheet->getStyle('B9:E9')->applyFromArray($underlineStyle);
            $sheet->getStyle('B9:E9')->applyFromArray($alignmentLeft); // Set left alignment
            $objPHPExcel->getActiveSheet()->getStyle("B9")->getFont()->setSize(12);
            $sheet->getStyle('A9')->getAlignment()->setWrapText(false);

            $sheet->setCellValue('A10', 'Semester: ');
            $sheet->getStyle('A10')->getFont()->setBold(true); // Set bold font
            $objPHPExcel->getActiveSheet()->getStyle("A10")->getFont()->setSize(12);
            $sheet->setCellValue('B10', $semesterText);
            $sheet->mergeCells('B10:E10');
            $sheet->getStyle('B10:E10')->applyFromArray($underlineStyle);
            $sheet->getStyle('B10:E10')->applyFromArray($alignmentLeft); // Set left alignment
            $objPHPExcel->getActiveSheet()->getStyle("B10")->getFont()->setSize(12);
            $sheet->getStyle('A10')->getAlignment()->setWrapText(false);

            $sheet->setCellValue('A11', 'Schedcode: ');
            $sheet->getStyle('A11')->getFont()->setBold(true); // Set bold font
            $objPHPExcel->getActiveSheet()->getStyle("A11")->getFont()->setSize(12);
            $sheet->setCellValue('B11', $schedcode_number);
            $sheet->mergeCells('B11:E11');
            $sheet->getStyle('B11:E11')->applyFromArray($underlineStyle);
            $sheet->getStyle('B11:E11')->applyFromArray($alignmentLeft); // Set left alignment
            $objPHPExcel->getActiveSheet()->getStyle("B11")->getFont()->setSize(12);
            $sheet->getStyle('A11')->getAlignment()->setWrapText(false);

            $sheet->setCellValue('A13', 'Student Number');
            // $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(13);
            $sheet->setCellValue('B13', 'Student Name');
            $sheet->setCellValue('C13', 'Component Name');
            $sheet->setCellValue('D13', 'Group Name');
            $sheet->setCellValue('E13', 'Student Grade');

            // Adjust column widths automatically for header
            foreach (range('A', 'E') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Add the section to the sectionWorksheets array with initial row counter
            $sectionWorksheets[$section] = $sectionIndex;
            $sectionRowCounter[$section] = 14;
        }

        // If the section has data, proceed to populate the rows
        if (isset($sectionWorksheets[$section])) {
            // Get the worksheet index and row counter for the current section
            $worksheetIndex = $sectionWorksheets[$section];
            $rowCounter = $sectionRowCounter[$section];

            // If the worksheet already exists, set the active sheet accordingly
            $objPHPExcel->setActiveSheetIndex($worksheetIndex);
            $sheet = $objPHPExcel->getActiveSheet();

            // Populate the rows with student list data for the current section
            $sheet->setCellValueExplicit('A' . $rowCounter, $row['student_number'] ?: 'No Data', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $rowCounter, $row['full_name'] ?: 'No Data');
            $sheet->setCellValue('C' . $rowCounter, $row['component_name'] ?: 'No Data');
            $sheet->setCellValue('D' . $rowCounter, $row['group_name'] ?: 'No Data');
            $sheet->setCellValue('E' . $rowCounter, $row['student_grade'] ?: 'No Grade');

            // Get the cell
            $cell = $sheet->getCell('E' . $rowCounter);

            // Check if the cell contains 'No Grade'
            if ($cell->getValue() == 'No Grade') {
                // Get the cell style and font
                $style = $cell->getStyle();
                $font = $style->getFont();

                // Set font color to red
                $font->getColor()->setRGB('FF0000');
            }

            // Increment the row counter for the next row
            $rowCounter++;

            // Store the updated row counter back in the array
            $sectionRowCounter[$section] = $rowCounter;
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
        $sheet->getStyle('A13:E' . ($rowCounter - 1))->applyFromArray($borderStyle);

        // Set the alignment for the entire range of cells
        $alignmentStyle = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('B' . ($rowCounter - 1) . ':E' . ($rowCounter - 1))->applyFromArray($alignmentStyle);
    }
} else {
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
    $columnG = 'G';
    $midpointColumn = chr(ord($columnA) + (ord($columnG) - ord($columnA)) / 6);
    $logo->setCoordinates($midpointColumn . '2'); // Position of the logo
    // Set the size of the image (adjust these values as needed)
    $logo->setWidth(160); // Set the width in pixels
    $logo->setHeight(100); // Set the height in pixels
    // Merge the cells to center the logo horizontally
    $sheet->mergeCells($columnA . '2:' . $columnG . '2');
    $logo->setOffsetX(-10); // Set the padding/margin to the right (adjust the value as needed)
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

    $sheet->setCellValue('A7', "$header");
    $sheet->mergeCells('A7:G7');
    $sheet->getStyle('A7')->getFont()->setBold(true); // Set bold font
    $objPHPExcel->getActiveSheet()->getStyle("A7")->getFont()->setSize(12);
    $sheet->getStyle('A7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $sheet->setCellValue('A9', 'No Student Grade data in this Course found');
    $sheet->getStyle('A9')->getFont()->setBold(true)->setSize(14);
    $sheet->mergeCells('A9:G9');
    $sheet->getStyle('A9')->applyFromArray([
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
    $sheet->getStyle('A9:G9')->applyFromArray($borderStyle);
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

ob_end_flush();
// Stop the script execution to prevent any additional output
exit;
?>
