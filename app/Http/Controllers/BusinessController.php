<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $query = Business::where('status', 'approved')->with(['owner', 'photos']);

        if ($request->has('category') && $request->category != 'all' && $request->category != '') {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $businesses = $query->latest()->paginate(12);
        $categories = Business::getCategories();

        return view('alumni.business.index', compact('businesses', 'categories'));
    }

    public function create()
    {
        $categories = Business::getCategories();
        return view('alumni.business.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'discount_info' => 'nullable|string|max:255',
            'whatsapp' => 'required|string',
            'website_url' => 'nullable|url|max:255',
            'location' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $logoUrl = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_logo_' . $file->getClientOriginalName();
            $path = $file->storeAs('businesses', $filename, 'public');
            $logoUrl = Storage::url($path);
        }

        $business = Business::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'discount_info' => $request->discount_info,
            'whatsapp' => $request->whatsapp,
            'website_url' => $request->website_url,
            'location' => $request->location,
            'logo_url' => $logoUrl,
            'status' => 'pending',
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                $photoPath = $photoFile->storeAs('businesses', time() . '_' . $photoFile->getClientOriginalName(), 'public');
                BusinessPhoto::create([
                    'business_id' => $business->id,
                    'photo_url' => Storage::url($photoPath)
                ]);
            }
        }

        return redirect()->route('alumni.business.index')->with('success', 'Usaha Anda berhasil didaftarkan dan sedang menunggu persetujuan Admin!');
    }

    public function show(Business $business)
    {
        // Allow access if business is approved, OR if viewer is the owner, OR if viewer is an admin
        if ($business->status !== 'approved' && 
            (!Auth::check() || (Auth::id() !== $business->user_id && Auth::user()->role !== 'admin'))) {
            abort(404);
        }
        
        return view('alumni.business.show', compact('business'));
    }

    public function edit(Business $business)
    {
        if ($business->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Business::getCategories();
        return view('alumni.business.edit', compact('business', 'categories'));
    }

    public function update(Request $request, Business $business)
    {
        if ($business->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string',
            'whatsapp' => 'required|string',
            'location' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = $request->only(['name', 'category', 'description', 'discount_info', 'whatsapp', 'website_url', 'location']);

        if ($request->hasFile('logo')) {
            if ($business->logo_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $business->logo_url));
            }
            $file = $request->file('logo');
            $path = $file->storeAs('businesses', time() . '_logo_' . $file->getClientOriginalName(), 'public');
            $data['logo_url'] = Storage::url($path);
        }

        $business->update($data);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                $photoPath = $photoFile->storeAs('businesses', time() . '_' . $photoFile->getClientOriginalName(), 'public');
                BusinessPhoto::create([
                    'business_id' => $business->id,
                    'photo_url' => Storage::url($photoPath)
                ]);
            }
        }

        return redirect()->route('alumni.business.show', $business->id)->with('success', 'Profil usaha berhasil diperbarui!');
    }

    public function destroy(Business $business)
    {
        if ($business->user_id !== Auth::id()) {
            abort(403);
        }

        if ($business->logo_url) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $business->logo_url));
        }
        
        foreach ($business->photos as $photo) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $photo->photo_url));
        }

        $business->delete();

        return redirect()->route('alumni.business.index')->with('success', 'Usaha Anda telah dihapus dari direktori.');
    }

    public function deletePhoto(BusinessPhoto $photo)
    {
        if ($photo->business->user_id !== Auth::id()) {
            abort(403);
        }

        Storage::disk('public')->delete(str_replace('/storage/', '', $photo->photo_url));
        $photo->delete();

        return back()->with('success', 'Foto produk berhasil dihapus.');
    }
}
