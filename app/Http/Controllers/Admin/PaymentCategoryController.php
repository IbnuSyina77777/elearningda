<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentCategory;
use Illuminate\Http\Request;

class PaymentCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentCategory::latest();
        
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(code) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) like ?', ["%{$search}%"]);
            });
        }
        
        $categories = $query->paginate(15)->withQueryString();
        return view('admin.payment-categories.index', compact('categories'));
    }

    public function create()
    {
        $classrooms = \App\Models\Classroom::with('major')->get();
        $academicYears = \App\Models\AcademicYear::all();
        return view('admin.payment-categories.create', compact('classrooms', 'academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id'=> 'required|exists:academic_years,id',
            'name'          => 'required|in:PTS,PAS,UJIKOM,SERAGAM',
            'semester'      => 'required|integer|min:1|max:6',
            'default_amount'=> 'required|numeric|min:0',
            'description'   => 'nullable|string',
            'classroom_id'  => 'nullable|string',
        ]);

        $codeMap = [
            'PTS' => 'PTS',
            'PAS' => 'PAS',
            'UJIKOM' => 'UJK',
            'SERAGAM' => 'SRG',
        ];

        // Format code menjadi contoh: PAS-1 (PAS Semester 1)
        $code = $codeMap[$validated['name']] . '-' . $validated['semester'];

        // Validasi duplikat agar tidak error SQL (termasuk soft-delete)
        $existing = PaymentCategory::withTrashed()
            ->where('code', $code)
            ->where('academic_year_id', $validated['academic_year_id'])
            ->first();
        $validated['code'] = $code;
        $validated['is_active'] = true;

        if ($existing) {
            if ($existing->trashed()) {
                // Otomatis kembalikan data yang ada di tong sampah (restore) dan timpa nilainya
                $existing->restore();
                $existing->update($validated);
                $category = $existing;
            } else {
                return back()->withInput()->withErrors(['name' => "Kategori {$validated['name']} untuk Semester {$validated['semester']} sudah pernah ditambahkan sebelumnya pada Tahun Ajaran ini."]);
            }
        } else {
            $category = PaymentCategory::create($validated);
        }

        if (!empty($validated['classroom_id'])) {
            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
            
            if (!$activeYear) {
                return redirect()->route('admin.payment-categories.index')
                    ->with('warning', 'Kategori berhasil ditambahkan, namun tagihan gagal di-generate otomatis karena tidak ada Tahun Ajaran aktif.');
            }

            $studentIds = [];
            $targetLevel = $category->target_level;

            if ($validated['classroom_id'] === 'all') {
                $studentIds = \App\Models\Student::whereHas('classroom', function($q) use ($targetLevel) {
                    if ($targetLevel) $q->where('level', $targetLevel);
                })->pluck('id')->toArray();
            } else {
                // Validate if specific class matches target level
                $selectedClass = \App\Models\Classroom::find($validated['classroom_id']);
                if ($targetLevel && $selectedClass && $selectedClass->level !== $targetLevel) {
                    return redirect()->route('admin.payment-categories.index')
                        ->with('warning', "Kategori berhasil ditambahkan, namun tagihan gagal dibuat karena kelas {$selectedClass->name} tidak sesuai dengan peruntukan Semester {$category->semester} (hanya untuk Kelas {$targetLevel}).");
                }
                
                $studentIds = \App\Models\Student::where('classroom_id', $validated['classroom_id'])->pluck('id')->toArray();
            }

            if (!empty($studentIds)) {
                $billsData = [];
                $now = now();
                $dueDate = now()->addDays(30);

                foreach ($studentIds as $id) {
                    $billsData[] = [
                        'student_id'          => $id,
                        'payment_category_id' => $category->id,
                        'academic_year_id'    => $activeYear->id,
                        'amount'              => $category->default_amount,
                        'due_date'            => $dueDate,
                        'status'              => 'unpaid',
                        'total_paid'          => 0,
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ];
                }

                foreach (array_chunk($billsData, 500) as $chunk) {
                    \App\Models\Bill::insertOrIgnore($chunk);
                }

                return redirect()->route('admin.payment-categories.index')
                    ->with('success', 'Kategori Pembayaran berhasil ditambahkan & '.count($studentIds).' tagihan telah digenerate otomatis.');
            }
        }

        return redirect()->route('admin.payment-categories.index')->with('success', 'Kategori Pembayaran berhasil ditambahkan.');
    }

    public function edit(PaymentCategory $paymentCategory)
    {
        $academicYears = \App\Models\AcademicYear::all();
        return view('admin.payment-categories.edit', compact('paymentCategory', 'academicYears'));
    }

    public function update(Request $request, PaymentCategory $paymentCategory)
    {
        $validated = $request->validate([
            'academic_year_id'=> 'required|exists:academic_years,id',
            'name'          => 'required|in:PTS,PAS,UJIKOM,SERAGAM',
            'semester'      => 'required|integer|min:1|max:6',
            'default_amount'=> 'required|numeric|min:0',
            'description'   => 'nullable|string',
        ]);

        $codeMap = [
            'PTS' => 'PTS',
            'PAS' => 'PAS',
            'UJIKOM' => 'UJK',
            'SERAGAM' => 'SRG',
        ];

        $code = $codeMap[$validated['name']] . '-' . $validated['semester'];

        // Validasi duplikat (mengabaikan ID saat ini, termasuk soft-delete)
        $existing = PaymentCategory::withTrashed()
            ->where('code', $code)
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('id', '!=', $paymentCategory->id)
            ->first();
        if ($existing) {
            if ($existing->trashed()) {
                // Hapus permanen data di tong sampah yang menghalangi perubahan ini
                $existing->forceDelete();
            } else {
                return back()->withInput()->withErrors(['name' => "Kategori {$validated['name']} Semester {$validated['semester']} sudah digunakan oleh data lain di Tahun Ajaran ini."]);
            }
        }

        $validated['code'] = $code;

        $paymentCategory->update($validated);

        return redirect()->route('admin.payment-categories.index')->with('success', 'Kategori Pembayaran berhasil diperbarui.');
    }

    public function destroy(PaymentCategory $paymentCategory)
    {
        if ($paymentCategory->bills()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus kategori yang sudah memiliki tagihan.');
        }

        $paymentCategory->delete();
        return redirect()->route('admin.payment-categories.index')->with('success', 'Kategori Pembayaran berhasil dihapus.');
    }
}
