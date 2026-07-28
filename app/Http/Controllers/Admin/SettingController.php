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
        $imageFields = ['app_logo', 'landing_hero_image', 'landing_about_image', 'login_bg_image'];
        $data = $request->except(array_merge(['_token', '_method'], $imageFields));

        // Handle normal text inputs
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'text']
            );
        }

        // Handle all image uploads
        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $path = $file->store('settings', 'public');

                // Delete old image if exists
                $oldSetting = Setting::where('key', $field)->first();
                if ($oldSetting && $oldSetting->value) {
                    Storage::disk('public')->delete($oldSetting->value);
                }

                Setting::updateOrCreate(
                    ['key' => $field],
                    ['value' => $path, 'type' => 'image']
                );
            }
        }

        // Clear cache
        Cache::forget('app_settings');

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}
