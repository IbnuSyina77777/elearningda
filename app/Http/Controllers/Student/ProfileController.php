<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        
        if (!$student) {
            return redirect('/')->with('error', 'Profil siswa tidak ditemukan.');
        }

        $student->load(['classroom', 'user']);

        return view('student.profile', compact('student'));
    }
}
