<?php
namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ActivityLog;
use App\Jobs\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class HeroController extends Controller
{
    public function edit()
    {
        $hero_title = Setting::get('hero_title', 'SELAMAT DATANG DI WEBSITE RESMI\nALUMNI SMKN 2 TERNATE');
        $hero_subtitle = Setting::get('hero_subtitle', 'Wadah komunikasi, informasi dan silaturahmi bagi seluruh keluarga besar alumni lintas angkatan.');
        $hero_background = Setting::get('hero_background', '/images/hero-bg.jpg');
        
        return view('admin.hero.edit', compact('hero_title', 'hero_subtitle', 'hero_background'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'hero_title' => 'required|string',
            'hero_subtitle' => 'required|string',
            'hero_background' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        Setting::updateOrCreate(
            ['key' => 'hero_title'], 
            ['value' => $request->hero_title, 'label' => 'Hero Title', 'group' => 'hero']
        );
        Setting::updateOrCreate(
            ['key' => 'hero_subtitle'], 
            ['value' => $request->hero_subtitle, 'label' => 'Hero Subtitle', 'group' => 'hero']
        );

        if ($request->hasFile('hero_background')) {
            $path = $request->file('hero_background')->store('uploads/hero', 'public');
            Setting::updateOrCreate(
                ['key' => 'hero_background'], 
                ['value' => '/storage/' . $path, 'label' => 'Hero Background', 'group' => 'hero']
            );
        }

        Artisan::call('optimize:clear');
        
        LogActivity::dispatch(
            Auth::id(),
            'Update Hero Section',
            'Updated Homepage Hero title, subtitle or background.',
            $request->ip(),
            $request->header('User-Agent')
        );

        return back()->with('success', 'Tampilan Beranda (Hero Section) berhasil diperbarui!');
    }
}
