<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::latest();

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->whereRaw('LOWER(title) like ?', ["%{$search}%"]);
        }

        if ($request->has('target_audience') && $request->target_audience != '') {
            $query->where('target_audience', $request->target_audience);
        }

        $announcements = $query->paginate(15)->withQueryString();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'content'         => 'required|string',
            'target_audience' => 'required|in:all,teachers,students',
            'is_active'       => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Announcement::create($validated);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'content'         => 'required|string',
            'target_audience' => 'required|in:all,teachers,students',
            'is_active'       => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
