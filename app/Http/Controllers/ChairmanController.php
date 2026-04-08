<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Jobs\LogActivity;

class ChairmanController extends Controller
{
    public function edit()
    {
        return view('admin.chairman.edit');
    }

    public function update(Request $request)
    {
        $request->validate([
            'chairman_name' => 'nullable|string|max:255',
            'chairman_period' => 'nullable|string|max:255',
            'chairman_message' => 'nullable|string',
            'alumni_message' => 'nullable|string',
            'chairman_photo' => 'nullable|image|max:5120',
            
            'event_chairman_name' => 'nullable|string|max:255',
            'event_chairman_period' => 'nullable|string|max:255',
            'event_chairman_message' => 'nullable|string',
            'event_chairman_photo' => 'nullable|image|max:5120',

            'secretary_name' => 'nullable|string|max:255',
            'secretary_period' => 'nullable|string|max:255',
            'secretary_message' => 'nullable|string',
            'secretary_photo' => 'nullable|image|max:5120',
        ]);

        try {
            // Alumni Chairman Settings
            $keys = ['chairman_name', 'chairman_period', 'chairman_message', 'alumni_message'];
            foreach ($keys as $key) {
                if ($request->has($key)) {
                    Setting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value' => $request->get($key),
                            'label' => ucwords(str_replace('_', ' ', $key)),
                            'group' => 'chairman'
                        ]
                    );
                }
            }

            if ($request->hasFile('chairman_photo')) {
                $path = $request->file('chairman_photo')->store('uploads/chairman', 'public');
                // Use relative path to avoid IP mismatch issues (APP_URL)
                $url = '/storage/' . $path;
                Setting::updateOrCreate(
                    ['key' => 'chairman_photo'], 
                    [
                        'value' => $url,
                        'label' => 'Foto Ketua Umum',
                        'group' => 'chairman'
                    ]
                );
            }

            // Event Committee Chairman Settings
            $eventKeys = ['event_chairman_name', 'event_chairman_period', 'event_chairman_message'];
            foreach ($eventKeys as $key) {
                if ($request->has($key)) {
                    Setting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value' => $request->get($key),
                            'label' => ucwords(str_replace('_', ' ', $key)),
                            'group' => 'event_chairman'
                        ]
                    );
                }
            }

            if ($request->hasFile('event_chairman_photo')) {
                $path = $request->file('event_chairman_photo')->store('uploads/chairman', 'public');
                // Use relative path to avoid IP mismatch issues (APP_URL)
                $url = '/storage/' . $path;
                Setting::updateOrCreate(
                    ['key' => 'event_chairman_photo'], 
                    [
                        'value' => $url,
                        'label' => 'Foto Ketua Panitia',
                        'group' => 'event_chairman'
                    ]
                );
            }

            // Secretary Settings
            $secretaryKeys = ['secretary_name', 'secretary_period', 'secretary_message'];
            foreach ($secretaryKeys as $key) {
                if ($request->has($key)) {
                    Setting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value' => $request->get($key),
                            'label' => ucwords(str_replace('_', ' ', $key)),
                            'group' => 'secretary'
                        ]
                    );
                }
            }

            if ($request->hasFile('secretary_photo')) {
                $path = $request->file('secretary_photo')->store('uploads/chairman', 'public');
                $url = '/storage/' . $path;
                Setting::updateOrCreate(
                    ['key' => 'secretary_photo'], 
                    [
                        'value' => $url,
                        'label' => 'Foto Sekretaris Panitia',
                        'group' => 'secretary'
                    ]
                );
            }

            Artisan::call('optimize:clear');

            LogActivity::dispatch(
                Auth::id(),
                'Update Chairman Settings',
                'Updated Alumni and Event Committee chairman details.',
                $request->ip(),
                $request->header('User-Agent')
            );

            return back()->with('success', 'Sambutan berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Chairman update failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Gagal memperbarui: ' . $e->getMessage()])->withInput();
        }
    }
}
