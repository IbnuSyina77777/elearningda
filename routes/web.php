<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;

// Landing Page
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // CRUD Siswa & Guru
    Route::get('students/export', [\App\Http\Controllers\Admin\StudentController::class, 'export'])->name('students.export');
    Route::post('students/import', [\App\Http\Controllers\Admin\StudentController::class, 'import'])->name('students.import');
    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);
    Route::get('alumni', [\App\Http\Controllers\Admin\AlumniController::class, 'index'])->name('alumni.index');
    Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class);
    Route::resource('materials', \App\Http\Controllers\Admin\MaterialController::class)->except(['show']);
    Route::resource('assignments', \App\Http\Controllers\Admin\AssignmentController::class)->except(['show']);
    
    Route::get('schedules/export', [\App\Http\Controllers\Admin\ScheduleController::class, 'export'])->name('schedules.export');
    Route::get('schedules/import', [\App\Http\Controllers\Admin\ScheduleController::class, 'showImportForm'])->name('schedules.import.form');
    Route::post('schedules/import', [\App\Http\Controllers\Admin\ScheduleController::class, 'import'])->name('schedules.import.process');
    Route::get('schedules/template', [\App\Http\Controllers\Admin\ScheduleController::class, 'template'])->name('schedules.template');
    Route::resource('schedules', \App\Http\Controllers\Admin\ScheduleController::class)->except(['show']);
    
    // Data Master
    Route::get('classrooms/{classroom}/promotion', [\App\Http\Controllers\Admin\ClassroomController::class, 'promotion'])->name('classrooms.promotion');
    Route::post('classrooms/{classroom}/promote', [\App\Http\Controllers\Admin\ClassroomController::class, 'promote'])->name('classrooms.promote');
    Route::resource('classrooms', \App\Http\Controllers\Admin\ClassroomController::class);
    Route::resource('academic-years', \App\Http\Controllers\Admin\AcademicYearController::class);
    
    // Pengaturan
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    
    // Grades Recap (Admin)
    Route::get('/grades', [\App\Http\Controllers\Admin\GradeController::class, 'index'])->name('grades.index');
    Route::get('/grades/export', [\App\Http\Controllers\Admin\GradeController::class, 'export'])->name('grades.export');
    
    Route::resource('payment-categories', \App\Http\Controllers\Admin\PaymentCategoryController::class);
    
    // E-Learning
    Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class);
    
    // Pantau Administrasi Guru
    Route::get('/teacher-administrations', [\App\Http\Controllers\Admin\TeacherAdministrationController::class, 'index'])->name('teacher-administrations.index');
    Route::get('/teacher-administrations/{teacher}/{subject}', [\App\Http\Controllers\Admin\TeacherAdministrationController::class, 'show'])->name('teacher-administrations.show');
    
    // Keuangan
    Route::post('bills/auto-sync', [\App\Http\Controllers\Admin\BillController::class, 'autoSync'])->name('bills.auto-sync');
    Route::delete('bills/destroy-all', [\App\Http\Controllers\Admin\BillController::class, 'destroyAll'])->name('bills.destroy-all');
    Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class)->except('show');
    
    // Laporan Keuangan
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('reports.exportPdf');
    
    // Attendances Report
    Route::get('attendances', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendances.index');
    
    Route::resource('bills', \App\Http\Controllers\Admin\BillController::class)->except(['edit', 'update']);
    Route::get('transactions/{id}/receipt', [\App\Http\Controllers\Admin\TransactionController::class, 'receipt'])->name('transactions.receipt');
    Route::resource('transactions', \App\Http\Controllers\Admin\TransactionController::class)->only(['index', 'store']);
});

// Teacher Routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    
    // Materials
    Route::get('/subjects/{subject}/materials', [\App\Http\Controllers\Teacher\MaterialController::class, 'index'])->name('materials.index');
    Route::get('/subjects/{subject}/materials/create', [\App\Http\Controllers\Teacher\MaterialController::class, 'create'])->name('materials.create');
    Route::post('/subjects/{subject}/materials', [\App\Http\Controllers\Teacher\MaterialController::class, 'store'])->name('materials.store');
    Route::get('/subjects/{subject}/materials/{material}/edit', [\App\Http\Controllers\Teacher\MaterialController::class, 'edit'])->name('materials.edit');
    Route::put('/subjects/{subject}/materials/{material}', [\App\Http\Controllers\Teacher\MaterialController::class, 'update'])->name('materials.update');
    Route::delete('/subjects/{subject}/materials/{material}', [\App\Http\Controllers\Teacher\MaterialController::class, 'destroy'])->name('materials.destroy');
    
    // Assignments
    Route::get('/subjects/{subject}/assignments', [\App\Http\Controllers\Teacher\AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/subjects/{subject}/assignments/create', [\App\Http\Controllers\Teacher\AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/subjects/{subject}/assignments', [\App\Http\Controllers\Teacher\AssignmentController::class, 'store'])->name('assignments.store');
    Route::delete('/subjects/{subject}/assignments/{assignment}', [\App\Http\Controllers\Teacher\AssignmentController::class, 'destroy'])->name('assignments.destroy');
    
    // Submissions (grading)
    Route::get('/subjects/{subject}/assignments/{assignment}/submissions', [\App\Http\Controllers\Teacher\AssignmentController::class, 'submissions'])->name('submissions.index');
    Route::post('/subjects/{subject}/assignments/{assignment}/submissions/{submission}/grade', [\App\Http\Controllers\Teacher\AssignmentController::class, 'grade'])->name('submissions.grade');

    // Grades Recap
    Route::get('/subjects/{subject}/grades', [\App\Http\Controllers\Teacher\GradeController::class, 'index'])->name('grades.index');
    Route::post('/subjects/{subject}/grades', [\App\Http\Controllers\Teacher\GradeController::class, 'storeGrades'])->name('grades.store');
    Route::get('/subjects/{subject}/grades/export', [\App\Http\Controllers\Teacher\GradeController::class, 'export'])->name('grades.export');
    
    // Akademik
    Route::resource('exams', \App\Http\Controllers\Teacher\ExamController::class);
    Route::resource('grades', \App\Http\Controllers\Teacher\GradeController::class);
    Route::resource('attendances', \App\Http\Controllers\Teacher\AttendanceController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('schedules', [\App\Http\Controllers\Teacher\ScheduleController::class, 'index'])->name('schedules.index');

    // Administrasi Guru
    Route::resource('administrations', \App\Http\Controllers\Teacher\AdministrationController::class)->only(['index', 'store', 'destroy']);
});

// Student Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    
    // Pembayaran
    Route::get('/bills', [\App\Http\Controllers\Student\BillController::class, 'index'])->name('bills.index');
    Route::get('/bills/{bill}', [\App\Http\Controllers\Student\BillController::class, 'show'])->name('bills.show');
    Route::post('/bills/{bill}/pay', [\App\Http\Controllers\Student\PaymentController::class, 'pay'])->name('bills.pay');
    Route::get('/payment/finish', [\App\Http\Controllers\Student\PaymentController::class, 'finish'])->name('payment.finish');
    Route::get('/transactions', [\App\Http\Controllers\Student\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{id}/receipt', [\App\Http\Controllers\Student\TransactionController::class, 'receipt'])->name('transactions.receipt');
    
    // E-Learning
    Route::get('/subjects', [\App\Http\Controllers\Student\SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/{subject}', [\App\Http\Controllers\Student\SubjectController::class, 'show'])->name('subjects.show');
    Route::get('/assignments/{assignment}', [\App\Http\Controllers\Student\AssignmentController::class, 'show'])->name('assignments.show');
    Route::post('/assignments/{assignment}/submit', [\App\Http\Controllers\Student\AssignmentController::class, 'submit'])->name('assignments.submit');
    Route::get('/transcript', [\App\Http\Controllers\Student\TranscriptController::class, 'index'])->name('transcript.index');
    Route::get('/schedules', [\App\Http\Controllers\Student\ScheduleController::class, 'index'])->name('schedules.index');
    
    // Profile
    Route::get('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'index'])->name('profile');
});

// Midtrans Webhook (no auth, no CSRF — called by Midtrans server)
Route::post('/api/midtrans/webhook', [\App\Http\Controllers\Webhook\MidtransController::class, 'handle'])
    ->name('midtrans.webhook');
