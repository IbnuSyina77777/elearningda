<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Bill;

class BillController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        if (!$student) {
            return redirect('/')->with('error', 'Profil siswa tidak ditemukan.');
        }

        $bills = $student->bills()->with(['paymentCategory', 'academicYear'])
                         ->orderBy('due_date', 'asc')
                         ->paginate(10);
                         
        return view('student.bills.index', compact('bills'));
    }

    public function show(Bill $bill)
    {
        $student = auth()->user()->student;
        
        // Ensure student can only see their own bill
        if ($bill->student_id !== $student->id) {
            abort(403, 'Akses ditolak.');
        }

        $bill->load(['paymentCategory', 'academicYear', 'transactions']);
        
        return view('student.bills.show', compact('bill'));
    }
}
