<?php
ob_start();
session_start();
include('../connection.php');
$con = connection();
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

    if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['downloadgrouplist'])){
        
        $schoolyear_id = $_POST['schoolyear_id'];
        $schoolyear_query = "SELECT * FROM schoolyeartable WHERE schoolyear_id = $schoolyear_id";
        $schoolyear_result = $con->query($schoolyear_query);
        $schoolyear_data = mysqli_fetch_assoc($schoolyear_result);

        $schoolyear_start = $schoolyear_data['schoolyear_start'];
        $schoolyear_end = $schoolyear_data['schoolyear_end'];
        $semester_id = $schoolyear_data['semester_id'];

        $semesterText = ($semester_id == 1) ? 'First Semester' : 'Second Semester';
        $current_schoolyear = $schoolyear_start . '-' . $schoolyear_end;

        $group_query = "SELECT g.*, t.*, g.group_id,
        (SELECT COUNT(*) 
        FROM useraccount 
        WHERE group_id = g.group_id AND role_account_id = 2) AS count_role_2
        FROM grouptable g
        LEFT JOIN useraccount t ON g.group_id = t.group_id AND t.role_account_id = 3
        WHERE g.schoolyear_id = $schoolyear_id AND g.semester_id = $semester_id
        ORDER BY g.component_id DESC, g.group_name;
        ";
        $group_result = $con->query($group_query);

        $ROTCGroup = "SELECT COUNT(*) AS rotcgroup FROM grouptable WHERE component_id = 1 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
        $ROTCGroup_result = $con->query($ROTCGroup);
        $ROTCGroup_data = $ROTCGroup_result->fetch_assoc();

        $CWTSGroup = "SELECT COUNT(*) AS cwtsgroup FROM grouptable WHERE component_id = 2 AND schoolyear_id = $schoolyear_id AND semester_id = $semester_id";
        $CWTSGroup_result = $con->query($CWTSGroup);
        $CWTSGroup_data = $CWTSGroup_result->fetch_assoc();

        $header = 'Group List';

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

        $sheet->setCellValue('A9', 'Number of ROTC Group: ');
        $sheet->getStyle('A9')->getFont()->setBold(true); // Set bold font
        $objPHPExcel->getActiveSheet()->getStyle("A9")->getFont()->setSize(12);
        $sheet->mergeCells('A9:B9');
        $sheet->setCellValue('C9', $ROTCGroup_data['rotcgroup']);
        $sheet->mergeCells('C9:G9');
        $sheet->getStyle('C9:G9')->applyFromArray($underlineStyle);
        $sheet->getStyle('C9:G9')->applyFromArray($alignmentLeft); // Set left alignment
        $objPHPExcel->getActiveSheet()->getStyle("C9")->getFont()->setSize(12);
        $sheet->getStyle('A9')->getAlignment()->setWrapText(false);

        $sheet->setCellValue('A10', 'Number of CWTS Group: ');
        $sheet->getStyle('A10')->getFont()->setBold(true); // Set bold font
        $objPHPExcel->getActiveSheet()->getStyle("A10")->getFont()->setSize(12);
        $sheet->mergeCells('A10:B10');
        $sheet->setCellValue('C10', $CWTSGroup_data['cwtsgroup']);
        $sheet->mergeCells('C10:G10');
        $sheet->getStyle('C10:G10')->applyFromArray($underlineStyle);
        $sheet->getStyle('C10:G10')->applyFromArray($alignmentLeft); // Set left alignment
        $objPHPExcel->getActiveSheet()->getStyle("C10")->getFont()->setSize(12);
        $sheet->getStyle('A10')->getAlignment()->setWrapText(false);

        $sheet->setCellValue('A12', 'Group Name');
        $sheet->getStyle('A12')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('A12:A13');
        $sheet->setCellValue('B12', 'Incharge Person Name');
        $sheet->getStyle('B12')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('B12:D12');
        $sheet->setCellValue('B13', 'Surname');
        $sheet->setCellValue('C13', 'First Name');
        $sheet->setCellValue('D13', 'Middle Initail');
        $sheet->getStyle('D13')->getAlignment()->setWrapText(true);
        $sheet->setCellValue('E12', 'Component Name');
        $sheet->getStyle('E12')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('E12:E13');
        $sheet->setCellValue('F12', 'Student Capacity');
        $sheet->getStyle('F12')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('F12:F13');
        $sheet->setCellValue('G12', 'Number of Student');
        $sheet->getStyle('G12')->getAlignment()->setWrapText(true);
        $sheet->mergeCells('G12:G13');

        $objPHPExcel->getActiveSheet()->getStyle("A12:G12")->getFont()->setSize(12);
        $sheet->getStyle('A12:G12')->applyFromArray($headerStyle);

        // Adjust column widths automatically for header
        // foreach (range('A', 'G') as $column) {
        //     $sheet->getColumnDimension($column)->setAutoSize(true);
        // }

        if ($group_result  && $group_result->num_rows > 0) {
            $rowCounter = 14; // Start populating data from the third row
            while ($row = $group_result->fetch_assoc()) {
                
                // Populate the rows with studentlist data
                $sheet->setCellValue('A' . $rowCounter, $row['group_name'] ?? 'No Data');
                // $sheet->setCellValue('A' . $rowCounter, $row['group_id'] ?? 'No Data');
                $sheet->setCellValue('B' . $rowCounter, $row['surname'] ?: 'No Data');
                $sheet->setCellValue('C' . $rowCounter, $row['firstname'] ?: 'No Data');
                // $sheet->setCellValue('D' . $rowCounter, $row['middlename'] ?: 'No Data');
                $sheet->setCellValue('D' . $rowCounter, isset($row['middlename']) && strlen($row['middlename']) > 0 ? strtoupper(substr($row['middlename'], 0, 1)) : 'No Data');
                $component = $row['component_id'];
                $componentLabel = ($component == 1) ? 'ROTC' : (($component == 2) ? 'CWTS' : 'No Data');
                $sheet->setCellValue('E' . $rowCounter, $componentLabel);
                $sheet->setCellValue('F' . $rowCounter, $row['number_student'] ?: 'No Data');
                $sheet->setCellValue('G' . $rowCounter, $row['count_role_2'] ?: 'No Student');


                $rowCounter++;

                foreach (range('A', 'G') as $column) {
                    if ($column === 'A') {
                        $sheet->getColumnDimension($column)->setWidth(15);
                    } elseif($column === 'B' || $column === 'C'){
                        $sheet->getColumnDimension($column)->setAutoSize(true); // Adjust the width as needed
                    }elseif($column === 'D'){
                        $sheet->getColumnDimension($column)->setWidth(10); // Adjust the width as needed
                    }elseif($column === 'E' || $column === 'F'){
                        $sheet->getColumnDimension($column)->setWidth(10); // Adjust the width as needed
                    } else {
                        $sheet->getColumnDimension($column)->setWidth(12); // Adjust the width as needed
                    }
                }

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
                $sheet->getStyle('A12:G' . ($rowCounter - 1))->applyFromArray($borderStyle);
                
                // Set the alignment for the entire range of cells
                $alignmentStyle = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];
                $sheet->getStyle('B'. ($rowCounter - 1).':G' . ($rowCounter - 1))->applyFromArray($alignmentStyle);
                
            }
        } else {
            $sheet->setCellValue('A4', 'No Group data found');
            $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(14);
            $sheet->mergeCells('A4:G4');
            $sheet->getStyle('A4')->applyFromArray([
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
            $sheet->getStyle('A1:G6')->applyFromArray($borderStyle);
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
    }
?>
