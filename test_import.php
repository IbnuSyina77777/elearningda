<?php
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\User;
use App\Models\Classroom;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;

// Create dummy xlsx file
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'NISN');
$sheet->setCellValue('B1', 'NIS');
$sheet->setCellValue('C1', 'Nama Lengkap');
$sheet->setCellValue('D1', 'Email');
$sheet->setCellValue('E1', 'Jenis Kelamin');
$sheet->setCellValue('F1', 'Telepon');
$sheet->setCellValue('G1', 'Alamat');
$sheet->setCellValue('H1', 'Nama Orang Tua');
$sheet->setCellValue('I1', 'Telepon Orang Tua');
$sheet->setCellValue('J1', 'Kelas');

$sheet->setCellValue('A2', '9999999999');
$sheet->setCellValue('B2', '8888');
$sheet->setCellValue('C2', 'Test Student');
$sheet->setCellValue('D2', 'teststudent@example.com');
$sheet->setCellValue('E2', 'L');
$sheet->setCellValue('F2', '08123456789');
$sheet->setCellValue('G2', 'Jl. Test');
$sheet->setCellValue('H2', 'Bapak Test');
$sheet->setCellValue('I2', '08123456789');
$sheet->setCellValue('J2', 'X-TKJ-1'); // Assume this exists

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save('dummy_students.xlsx');

try {
    $rows = \Maatwebsite\Excel\Facades\Excel::toArray(new \App\Imports\StudentsImport, 'dummy_students.xlsx')[0];
    echo "Rows read: " . count($rows) . "\n";
    print_r($rows);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
