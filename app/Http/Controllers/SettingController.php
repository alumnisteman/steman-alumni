<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Jobs\LogActivity;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group')->sortBy(function($items, $key) {
            $priority = [
                'general' => 1,
                'profile' => 2,
                'hero' => 3,
                'contact' => 4,
                'chairman' => 5,
                'event_chairman' => 6,
                'secretary' => 7,
                'program' => 8,
                'ai' => 9
            ];
            return $priority[$key] ?? 100;
        });
        return view('admin.settings.index', compact('settings'));
    }

    public function contact()
    {
        $settings = Setting::where('group', 'contact')
            ->get()
            ->sortBy('id'); // Default order by creation
        return view('admin.settings.contact', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|unique:settings,key',
            'label' => 'required',
            'group' => 'required'
        ]);

        Setting::create($request->all());

        Artisan::call('optimize:clear');
        
        return back()->with('success', 'Informasi baru berhasil ditambahkan.');
    }

    public function update(Request $request)
    {
        $settings = $request->except('_token', '_method');
        
        foreach ($settings as $key => $value) {
            // Only process files if a file was actually uploaded and valid
            if ($request->hasFile($key) && $request->file($key)->isValid()) {
                $path = $request->file($key)->store('uploads/settings', 'public');
                $value = '/storage/' . $path;
            }
            // Only update if the key exists in the settings table
            Setting::where('key', $key)->update(['value' => $value ?? '']);
        }

        // Clear all possible caches so settings reflect immediately (Self-Healing)
        try { 
            Artisan::call('optimize:clear'); 
            \Illuminate\Support\Facades\Cache::flush();
        } catch (\Exception $e) {}

        // Log the activity (non-critical)
        try {
            LogActivity::dispatch(
                Auth::id(),
                'Update Settings',
                'Updated site settings.',
                $request->ip(),
                $request->header('User-Agent')
            );
        } catch (\Exception $e) {}
        
        return back()->with('success', 'Pengaturan situs berhasil diperbarui.');
    }
}
