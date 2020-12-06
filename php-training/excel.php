<?php
require "./functions.php";
spl_autoload_register(function ($className) {
    require './PHPExcel-1.8/Classes/' . $className . '.php';
});

$data = getUsers();
$excel = new PHPExcel();

$excel->setActiveSheetIndex(0);
date_default_timezone_set("Asia/Ho_Chi_Minh");
$date = date("d-m-Y_H-i");
$sheet = $excel->getActiveSheet()->setTitle($date);
$sheet->getColumnDimension('A')->setWidth(10);
$sheet->getColumnDimension('B')->setWidth(30);
$sheet->getColumnDimension('C')->setWidth(30);
$sheet->getColumnDimension('D')->setWidth(40);
$sheet->getColumnDimension('E')->setWidth(10);
$sheet->getStyle('A1:E1')->getFont()->setBold(true);
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Username');
$sheet->setCellValue('C1', 'Fullname');
$sheet->setCellValue('D1', 'Email');
$sheet->setCellValue('E1', 'Absent');
$numRow = 2;
foreach ($data as $row) {
    $sheet->setCellValue('A' . $numRow, $row['id']);
    $sheet->setCellValue('B' . $numRow, $row['username']);
    $sheet->setCellValue('C' . $numRow, $row['fullname']);
    $sheet->setCellValue('D' . $numRow, $row['email']);
    $sheet->setCellValue('E' . $numRow, $row['vang']);
    $numRow++;
}
$styleArray = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);
$sheet->getStyle('A1:'.'E'.($numRow - 1 ))->applyFromArray($styleArray);
header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename=' . $date .'.xlsx');
PHPExcel_IOFactory::createWriter($excel, 'Excel2007')->save('php://output');