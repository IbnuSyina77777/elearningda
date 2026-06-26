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
        return view('admin.payment-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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

        // Format code menjadi contoh: PAS-1 (PAS Semester 1)
        $code = $codeMap[$validated['name']] . '-' . $validated['semester'];

        // Validasi duplikat agar tidak error SQL 1062
        if (PaymentCategory::where('code', $code)->exists()) {
            return back()->withInput()->withErrors(['name' => "Kategori {$validated['name']} untuk Semester {$validated['semester']} sudah pernah ditambahkan sebelumnya. Silakan edit data yang sudah ada."]);
        }

        $validated['code'] = $code;
        $validated['is_active'] = true;

        PaymentCategory::create($validated);

        return redirect()->route('admin.payment-categories.index')->with('success', 'Kategori Pembayaran berhasil ditambahkan.');
    }

    public function edit(PaymentCategory $paymentCategory)
    {
        return view('admin.payment-categories.edit', compact('paymentCategory'));
    }

    public function update(Request $request, PaymentCategory $paymentCategory)
    {
        $validated = $request->validate([
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

        // Validasi duplikat (mengabaikan ID saat ini)
        if (PaymentCategory::where('code', $code)->where('id', '!=', $paymentCategory->id)->exists()) {
            return back()->withInput()->withErrors(['name' => "Kategori {$validated['name']} Semester {$validated['semester']} sudah digunakan oleh data lain."]);
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
