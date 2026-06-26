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
        $totalStudents = Student::count();
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
            'totalBillsAmount', 
            'totalPaidAmount', 
            'overdueCount',
            'recentTransactions',
            'chartData'
        ));
    }
}
