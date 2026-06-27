<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Student;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::where('status', '!=', 'alumni')->count();
        
        $studentsPerLevel = Student::join('classrooms', 'students.classroom_id', '=', 'classrooms.id')
            ->where('students.status', '!=', 'alumni')
            ->selectRaw('classrooms.level, count(students.id) as total')
            ->groupBy('classrooms.level')
            ->pluck('total', 'level');

        $billsPerLevel = Bill::join('students', 'bills.student_id', '=', 'students.id')
            ->join('classrooms', 'students.classroom_id', '=', 'classrooms.id')
            ->where('students.status', '!=', 'alumni')
            ->selectRaw('classrooms.level, sum(bills.amount) as total_amount, sum(bills.total_paid) as total_paid')
            ->groupBy('classrooms.level')
            ->get()
            ->keyBy('level');

        $totalAlumni = Student::where('status', 'alumni')->count();
        $totalAlumniBillsAmount = Bill::join('students', 'bills.student_id', '=', 'students.id')
            ->where('students.status', 'alumni')
            ->sum('bills.amount');
        $totalAlumniPaidAmount = Bill::join('students', 'bills.student_id', '=', 'students.id')
            ->where('students.status', 'alumni')
            ->sum('bills.total_paid');

        $totalBillsAmount = Bill::sum('amount');
        $totalPaidAmount = Bill::sum('total_paid');
        $overdueCount = Bill::overdue()->count();
        
        $recentTransactions = Transaction::with(['bill.student'])
            ->latest()
            ->take(5)
            ->get();

        // Dummy data for revenue chart (last 6 months)
        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            'values' => [1500000, 2200000, 1800000, 3100000, 2500000, 4200000]
        ];

        return view('admin.dashboard', compact(
            'totalStudents', 
            'studentsPerLevel',
            'billsPerLevel',
            'totalAlumni',
            'totalAlumniBillsAmount',
            'totalAlumniPaidAmount',
            'totalBillsAmount', 
            'totalPaidAmount', 
            'overdueCount',
            'recentTransactions',
            'chartData'
        ));
    }
}
