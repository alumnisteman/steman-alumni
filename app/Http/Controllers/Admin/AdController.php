<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Jobs\LogActivity;

class AdController extends Controller
{
    protected $imageService;

    public function __construct(\App\Services\AdsImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the ads.
     */
    public function index()
    {
        $ads = Ad::latest()->paginate(10);
        return view('admin.ads.index', compact('ads'));
    }

    /**
     * Show the form for creating a new ad.
     */
    public function create()
    {
        return view('admin.ads.create');
    }

    /**
     * Store a newly created ad in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'image_desktop'  => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'image_mobile'   => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'link'           => 'nullable|url',
            'position'       => 'required|string|in:header,sidebar,footer,content,popup',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'is_active'      => 'nullable',
            'desktop_offset_x' => 'nullable|integer',
            'desktop_offset_y' => 'nullable|integer',
            'desktop_zoom'     => 'nullable|numeric',
            'mobile_offset_x'  => 'nullable|integer',
            'mobile_offset_y'  => 'nullable|integer',
            'mobile_zoom'      => 'nullable|numeric',
        ]);

        try {
            // Process Desktop Image
            if ($request->hasFile('image_desktop')) {
                $data['image_desktop'] = $this->imageService->process($request->file('image_desktop'), $data['position'], false, [
                    'offset_x' => $request->desktop_offset_x,
                    'offset_y' => $request->desktop_offset_y,
                    'zoom'     => $request->desktop_zoom
                ]);
            }

            // Process Mobile Image (or auto-generate if missing)
            if ($request->hasFile('image_mobile')) {
                $data['image_mobile'] = $this->imageService->process($request->file('image_mobile'), $data['position'], true, [
                    'offset_x' => $request->mobile_offset_x,
                    'offset_y' => $request->mobile_offset_y,
                    'zoom'     => $request->mobile_zoom
                ]);
            } else {
                // Auto-generate mobile version from desktop
                $data['image_mobile'] = $this->imageService->autoGenerateMobile($data['image_desktop'], $data['position'], [
                    'offset_x' => $request->mobile_offset_x ?? 50,
                    'offset_y' => $request->mobile_offset_y ?? 50,
                    'zoom'     => $request->mobile_zoom ?? 1.0
                ]);
            }

            $data['is_active'] = $request->boolean('is_active', true);

            $ad = Ad::create($data);

            LogActivity::dispatch(
                Auth::id(),
                'Create Ad',
                'Added advertisement: ' . $ad->title,
                $request->ip(),
                $request->header('User-Agent')
            );

            Cache::forget('active_ads');

            return redirect()->route('admin.ads.index')->with('success', 'Iklan berhasil ditambahkan dengan optimasi otomatis.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AdController Store Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            return back()->withInput()->with('error', 'Gagal menyimpan iklan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified ad (Redirect to edit).
     */
    public function show(Ad $ad)
    {
        return redirect()->route('admin.ads.edit', $ad);
    }

    /**
     * Show the form for editing the specified ad.
     */
    public function edit(Ad $ad)
    {
        return view('admin.ads.edit', compact('ad'));
    }

    /**
     * Update the specified ad in storage.
     */
    public function update(Request $request, Ad $ad)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'image_desktop'  => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'image_mobile'   => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'link'           => 'nullable|url',
            'position'       => 'required|string|in:header,sidebar,footer,content,popup',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'is_active'      => 'nullable',
            'desktop_offset_x' => 'nullable|integer',
            'desktop_offset_y' => 'nullable|integer',
            'desktop_zoom'     => 'nullable|numeric',
            'mobile_offset_x'  => 'nullable|integer',
            'mobile_offset_y'  => 'nullable|integer',
            'mobile_zoom'      => 'nullable|numeric',
        ]);

        try {
            if ($request->hasFile('image_desktop')) {
                // Delete old desktop image
                if ($ad->getRawOriginal('image_desktop')) {
                    Storage::disk('public')->delete($ad->getRawOriginal('image_desktop'));
                }
                $data['image_desktop'] = $this->imageService->process($request->file('image_desktop'), $data['position'], false, [
                    'offset_x' => $request->desktop_offset_x,
                    'offset_y' => $request->desktop_offset_y,
                    'zoom'     => $request->desktop_zoom
                ]);
                
                // If no new mobile image provided, auto-regenerate it from the new desktop image
                if (!$request->hasFile('image_mobile')) {
                    if ($ad->getRawOriginal('image_mobile')) {
                        Storage::disk('public')->delete($ad->getRawOriginal('image_mobile'));
                    }
                    $data['image_mobile'] = $this->imageService->autoGenerateMobile($data['image_desktop'], $data['position'], [
                        'offset_x' => $request->mobile_offset_x ?? 50,
                        'offset_y' => $request->mobile_offset_y ?? 50,
                        'zoom'     => $request->mobile_zoom ?? 1.0
                    ]);
                }
            }

            if ($request->hasFile('image_mobile')) {
                // Delete old mobile image
                if ($ad->getRawOriginal('image_mobile')) {
                    Storage::disk('public')->delete($ad->getRawOriginal('image_mobile'));
                }
                $data['image_mobile'] = $this->imageService->process($request->file('image_mobile'), $data['position'], true, [
                    'offset_x' => $request->mobile_offset_x,
                    'offset_y' => $request->mobile_offset_y,
                    'zoom'     => $request->mobile_zoom
                ]);
            }

            $data['is_active'] = $request->boolean('is_active', false);

            $ad->update($data);

            LogActivity::dispatch(
                Auth::id(),
                'Update Ad',
                'Updated advertisement: ' . $ad->title,
                $request->ip(),
                $request->header('User-Agent')
            );

            Cache::forget('active_ads');

            return redirect()->route('admin.ads.index')->with('success', 'Iklan berhasil diperbarui dengan optimasi otomatis.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AdController Update Error: ' . $e->getMessage(), [
                'exception' => $e,
                'ad_id' => $ad->id,
                'request' => $request->all()
            ]);
            return back()->withInput()->with('error', 'Gagal memperbarui iklan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified ad from storage.
     */
    public function destroy(Ad $ad)
    {
        try {
            if ($ad->image) {
                // Revolve path before delete
                $oldPath = $ad->getRawOriginal('image');
                if ($oldPath) Storage::disk('public')->delete($oldPath);
            }
            
            if ($ad->mobile_image) {
                $oldMobilePath = $ad->getRawOriginal('mobile_image');
                if ($oldMobilePath) Storage::disk('public')->delete($oldMobilePath);
            }

            $title = $ad->title;
            $ad->delete();

            LogActivity::dispatch(
                Auth::id(),
                'Delete Ad',
                'Deleted advertisement: ' . $title,
                request()->ip(),
                request()->header('User-Agent')
            );

            Cache::forget('active_ads');

            return back()->with('success', 'Iklan berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AdController Destroy Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus iklan.');
        }
    }
}

