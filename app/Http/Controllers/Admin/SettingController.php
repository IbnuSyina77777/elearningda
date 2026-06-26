<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token', '_method', 'logo');

        // Handle normal inputs
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'text']
            );
        }

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            $file = $request->file('app_logo');
            $path = $file->store('settings', 'public');
            
            // Delete old logo if exists
            $oldLogo = Setting::where('key', 'app_logo')->first();
            if ($oldLogo && $oldLogo->value) {
                Storage::disk('public')->delete($oldLogo->value);
            }

            Setting::updateOrCreate(
                ['key' => 'app_logo'],
                ['value' => $path, 'type' => 'image']
            );
        }

        // Clear cache
        Cache::forget('app_settings');

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
