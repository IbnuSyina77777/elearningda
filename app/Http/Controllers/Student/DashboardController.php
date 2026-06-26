<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            return redirect('/')->with('error', 'Profil siswa tidak ditemukan.');
        }

        $bills = $student->bills()->with('paymentCategory')->get();
        
        $totalBills = $bills->sum('amount');
        $totalPaid = $bills->sum('total_paid');
        $totalRemaining = $totalBills - $totalPaid;

        $activeBills = $bills->where('status', '!=', 'paid')->take(3);
        $recentTransactions = $student->transactions()->latest()->take(3)->get();

        $announcements = \App\Models\Announcement::where('is_active', true)
            ->whereIn('target_audience', ['all', 'students'])
            ->latest()
            ->take(5)
            ->get();

        return view('student.dashboard', compact(
            'student',
            'totalBills',
            'totalPaid',
            'totalRemaining',
            'activeBills',
            'recentTransactions',
            'announcements'
        ));
    }
}
