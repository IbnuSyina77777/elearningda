<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Bill;
use App\Models\Classroom;
use App\Models\PaymentCategory;
use App\Models\Student;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = Bill::with(['student.classroom', 'paymentCategory', 'academicYear']);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('classroom_id') && $request->classroom_id != '') {
            $classroomId = $request->classroom_id;
            $query->whereHas('student', function($q) use ($classroomId) {
                $q->where('classroom_id', $classroomId);
            });
        }

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->whereHas('student', function($q) use ($search) {
                $q->where(function($sq) use ($search) {
                    $sq->whereHas('user', function($uq) use ($search) {
                        $uq->whereRaw('LOWER(name) like ?', ["%{$search}%"]);
                    })
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
                });
            });
        }
        if ($request->filled('payment_category_id')) {
            $query->where('payment_category_id', $request->payment_category_id);
        }

        $bills = $query->latest()->paginate(20)->withQueryString();
        $classrooms = Classroom::with('major')->get();
        $categories = PaymentCategory::orderBy('name')->get();

        return view('admin.bills.index', compact('bills', 'classrooms', 'categories'));
    }

    public function create()
    {
        $categories = PaymentCategory::all();
        $academicYears = AcademicYear::all();
        $classrooms = Classroom::with('major')->get();
        
        return view('admin.bills.create', compact('categories', 'academicYears', 'classrooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_category_id' => 'required|exists:payment_categories,id',
            'academic_year_id'    => 'required|exists:academic_years,id',
            'amount'              => 'required|numeric|min:0',
            'due_date'            => 'required|date',
            'target_type'         => 'required|in:classroom,student',
            'classroom_id'        => 'required_if:target_type,classroom',
            'student_id'          => 'required_if:target_type,student|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $studentIds = [];

            $category = PaymentCategory::find($validated['payment_category_id']);
            $targetLevel = $category ? $category->target_level : null;

            if ($request->target_type === 'classroom') {
                if ($request->classroom_id === 'all') {
                    $studentIds = Student::whereHas('classroom', function($q) use ($targetLevel) {
                        if ($targetLevel) $q->where('level', $targetLevel);
                    })->pluck('id')->toArray();
                } else {
                    $selectedClass = \App\Models\Classroom::find($request->classroom_id);
                    if ($targetLevel && $selectedClass && $selectedClass->level !== $targetLevel) {
                        return back()->with('error', "Kelas {$selectedClass->name} tidak sesuai dengan peruntukan Kategori {$category->name} Semester {$category->semester} (hanya untuk Kelas {$targetLevel}).")->withInput();
                    }
                    $studentIds = Student::where('classroom_id', $request->classroom_id)->pluck('id')->toArray();
                }
                
                if (empty($studentIds)) {
                    return back()->with('error', 'Kelas yang dipilih tidak memiliki siswa.')->withInput();
                }
            } else {
                // Future expansion: single student selection
                return back()->with('error', 'Target individu belum diimplementasikan di antarmuka ini. Gunakan target kelas.');
            }

            $billsData = [];
            $now = now();
            
            // Cek siswa mana saja yang sudah punya tagihan ini agar tidak duplikat (menghindari error SQL 1062)
            $existingStudentIds = Bill::withTrashed()
                ->whereIn('student_id', $studentIds)
                ->where('payment_category_id', $validated['payment_category_id'])
                ->where('academic_year_id', $validated['academic_year_id'])
                ->pluck('student_id')
                ->toArray();
                
            $studentIdsToBill = array_diff($studentIds, $existingStudentIds);
            
            if (empty($studentIdsToBill)) {
                return back()->with('error', 'Semua siswa di kelas ini sudah memiliki tagihan untuk kategori dan tahun ajaran yang dipilih. Tidak ada tagihan baru yang dibuat.')->withInput();
            }

            foreach ($studentIdsToBill as $id) {
                $billsData[] = [
                    'student_id'          => $id,
                    'payment_category_id' => $validated['payment_category_id'],
                    'academic_year_id'    => $validated['academic_year_id'],
                    'amount'              => $validated['amount'],
                    'due_date'            => $validated['due_date'],
                    'status'              => 'unpaid',
                    'total_paid'          => 0,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ];
            }

            foreach (array_chunk($billsData, 500) as $chunk) {
                Bill::insertOrIgnore($chunk);
            }

            DB::commit();
            
            $msg = count($billsData) . ' tagihan berhasil digenerate secara massal.';
            if (count($existingStudentIds) > 0) {
                $msg .= ' (' . count($existingStudentIds) . ' siswa dilewati karena sudah memiliki tagihan ini).';
            }
            
            return redirect()->route('admin.bills.index')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Otomatis mengenerate tagihan berdasarkan kategori yang ada untuk seluruh siswa.
     */
    public function autoSync()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return back()->with('error', 'Tidak ada Tahun Ajaran yang aktif. Silakan set salah satu tahun ajaran menjadi aktif terlebih dahulu.');
        }

        $categories = PaymentCategory::where('is_active', true)
                        ->where('academic_year_id', $activeYear->id)
                        ->get();
        if ($categories->isEmpty()) {
            return back()->with('error', 'Belum ada kategori pembayaran yang aktif.');
        }

        $students = Student::with('classroom')->get();
        if ($students->isEmpty()) {
            return back()->with('error', 'Belum ada data siswa di dalam sistem.');
        }

        $count = 0;
        $now = now();
        $dueDate = now()->addDays(30);
        $billsData = [];

        try {
            DB::beginTransaction();

            foreach ($students as $student) {
                // Ambil ID kategori yang sudah dimiliki siswa ini di tahun ajaran aktif (termasuk yg soft-deleted)
                $existingCategoryIds = Bill::withTrashed()
                    ->where('student_id', $student->id)
                    ->where('academic_year_id', $activeYear->id)
                    ->pluck('payment_category_id')
                    ->toArray();

                foreach ($categories as $category) {
                    $targetLevel = $category->target_level;
                    if ($targetLevel && $student->classroom && $student->classroom->level !== $targetLevel) {
                        continue; // Skip because the category is not for this student's grade level
                    }

                    // Jika siswa belum punya tagihan untuk kategori ini, buatkan
                    if (!in_array($category->id, $existingCategoryIds)) {
                        $billsData[] = [
                            'student_id'          => $student->id,
                            'payment_category_id' => $category->id,
                            'academic_year_id'    => $activeYear->id,
                            'amount'              => $category->default_amount,
                            'due_date'            => $dueDate,
                            'status'              => 'unpaid',
                            'total_paid'          => 0,
                            'created_at'          => $now,
                            'updated_at'          => $now,
                        ];
                        $count++;
                    } else {
                        // Jika sudah ada tapi di tong sampah, restore
                        $trashedBill = Bill::onlyTrashed()
                            ->where('student_id', $student->id)
                            ->where('payment_category_id', $category->id)
                            ->where('academic_year_id', $activeYear->id)
                            ->first();
                        if ($trashedBill) {
                            $trashedBill->restore();
                            $count++;
                        }
                    }
                }
            }

            if (!empty($billsData)) {
                // Insert dalam bongkahan (chunks) agar memori aman jika data sangat banyak
                foreach (array_chunk($billsData, 500) as $chunk) {
                    Bill::insertOrIgnore($chunk);
                }
            }

            DB::commit();

            if ($count === 0) {
                return back()->with('success', 'Sistem sudah tersinkronisasi. Semua siswa sudah memiliki tagihan yang lengkap sesuai kategori yang ada.');
            }

            return back()->with('success', "$count tagihan baru berhasil di-generate secara otomatis untuk seluruh siswa.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat Auto-Sync: ' . $e->getMessage());
        }
    }

    public function show(Bill $bill)
    {
        $bill->load(['student.classroom', 'paymentCategory', 'academicYear', 'transactions']);
        return view('admin.bills.show', compact('bill'));
    }

    public function destroy(Bill $bill)
    {
        try {
            DB::beginTransaction();
            
            // Hapus juga semua transaksi yang terkait dengan tagihan ini (Soft Delete)
            if ($bill->transactions()->count() > 0) {
                $bill->transactions()->delete();
            }

            $bill->delete();
            
            DB::commit();
            return redirect()->route('admin.bills.index')->with('success', 'Tagihan beserta riwayat pembayarannya berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menghapus tagihan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus SELURUH data tagihan dan transaksinya.
     */
    public function destroyAll()
    {
        try {
            DB::beginTransaction();
            
            // Soft delete seluruh tabel transaksi
            \App\Models\Transaction::query()->delete();
            
            // Soft delete seluruh tabel tagihan
            \App\Models\Bill::query()->delete();
            
            DB::commit();
            return redirect()->route('admin.bills.index')->with('success', 'Seluruh data tagihan dan riwayat transaksi telah dikosongkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mengosongkan tagihan: ' . $e->getMessage());
        }
    }
}
