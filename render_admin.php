<?php
use Illuminate\Http\Request;

$request = Request::create('/admin/grades', 'GET');
$controller = app()->make(\App\Http\Controllers\Admin\GradeController::class);
$response = $controller->index($request);
$html = $response->render();

file_put_contents('c:\web\elearning\admin_grade_output.html', $html);
echo "HTML rendered and saved.";
