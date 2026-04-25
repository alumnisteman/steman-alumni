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
                'launch' => 4,
                'contact' => 5,
                'chairman' => 6,
                'event_chairman' => 7,
                'secretary' => 8,
                'program' => 9,
                'ai' => 10
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
            
            // Use updateOrCreate for better resilience (Self-Healing)
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '', 'group' => 'general'] // Default group if new
            );
        }

        // Clear all caches so settings reflect immediately
        try { 
            Artisan::call('optimize:clear'); 
            \Illuminate\Support\Facades\Cache::flush();
            \Illuminate\Support\Facades\Cache::forget('admin_dashboard_stats');
        } catch (\Exception $e) {}

        // Log the activity
        try {
            LogActivity::dispatch(
                Auth::id(),
                'Update Settings',
                'Updated site settings: ' . implode(', ', array_keys($settings)),
                $request->ip(),
                $request->header('User-Agent')
            );
        } catch (\Exception $e) {}
        
        return back()->with('success', 'Pengaturan situs berhasil diperbarui.');
    }
}
