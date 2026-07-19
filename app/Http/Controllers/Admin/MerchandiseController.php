<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchandise;
use App\Models\MerchandiseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MerchandiseController extends Controller
{
    public function index()
    {
        $merchandise = Merchandise::withTrashed()->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.merchandise.index', compact('merchandise'));
    }

    public function create()
    {
        $categories = Merchandise::getCategories();
        return view('admin.merchandise.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateMerchandise($request);
        $data = $this->handleMainImage($request, $data);
        $data['slug']   = Str::slug($request->name) . '-' . Str::random(6);
        $data['sizes']  = $request->filled('sizes')  ? array_values(array_filter(array_map('trim', explode(',', $request->sizes))))  : null;
        $data['colors'] = $request->filled('colors') ? array_values(array_filter(array_map('trim', explode(',', $request->colors)))) : null;

        $merchandise = Merchandise::create($data);

        // Gallery images
        if ($request->hasFile('gallery')) {
            $this->storeGalleryImages($request, $merchandise);
        }

        return redirect()->route('admin.merchandise.index')->with('success', 'Merchandise berhasil ditambahkan.');
    }

    public function edit(Merchandise $merchandise)
    {
        $categories = Merchandise::getCategories();
        return view('admin.merchandise.form', compact('merchandise', 'categories'));
    }

    public function update(Request $request, Merchandise $merchandise)
    {
        $data = $this->validateMerchandise($request, $merchandise->id);
        $data = $this->handleMainImage($request, $data, $merchandise);
        $data['sizes']  = $request->filled('sizes')  ? array_values(array_filter(array_map('trim', explode(',', $request->sizes))))  : null;
        $data['colors'] = $request->filled('colors') ? array_values(array_filter(array_map('trim', explode(',', $request->colors)))) : null;

        $merchandise->update($data);

        // Append new gallery images
        if ($request->hasFile('gallery')) {
            $this->storeGalleryImages($request, $merchandise);
        }

        return redirect()
            ->route('admin.merchandise.edit', $merchandise)
            ->with('success', 'Merchandise berhasil diperbarui.');
    }

    public function destroy(Merchandise $merchandise)
    {
        $merchandise->delete();
        return redirect()->route('admin.merchandise.index')->with('success', 'Merchandise berhasil dihapus.');
    }

    /** Delete one gallery photo by its index in the JSON array */
    public function deleteGalleryPhoto(Request $request, Merchandise $merchandise)
    {
        $index  = (int) $request->input('index');
        $images = $merchandise->images ?? [];

        if (isset($images[$index])) {
            $oldPath = ltrim(str_replace('/storage/', '', $images[$index]), '/');
            Storage::disk('public')->delete($oldPath);
            array_splice($images, $index, 1);
            $merchandise->update(['images' => array_values($images)]);
        }

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    /** Set a gallery photo as the main product image */
    public function setMainImage(Request $request, Merchandise $merchandise)
    {
        $index  = (int) $request->input('index');
        $images = $merchandise->images ?? [];

        if (isset($images[$index])) {
            $newMain = $images[$index];
            // Swap: put old main into gallery if it exists
            $oldImages = $images;
            array_splice($oldImages, $index, 1);
            if ($merchandise->image) {
                array_unshift($oldImages, $merchandise->image);
            }
            $merchandise->update([
                'image'  => $newMain,
                'images' => array_values($oldImages),
            ]);
        }

        return back()->with('success', 'Foto utama berhasil diubah.');
    }

    // ─────── Orders ──────────────────────────────────────────────────

    public function orders(Request $request)
    {
        $query = MerchandiseOrder::with('merchandise')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_code', 'like', '%' . $request->search . '%')
                  ->orWhere('buyer_name',  'like', '%' . $request->search . '%')
                  ->orWhere('buyer_phone', 'like', '%' . $request->search . '%');
            });
        }

        $orders   = $query->paginate(25);
        $statuses = ['pending', 'confirmed', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'];

        return view('admin.merchandise.orders', compact('orders', 'statuses'));
    }

    public function updateOrderStatus(Request $request, MerchandiseOrder $order)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,paid,processing,shipped,delivered,cancelled']);
        $order->update([
            'status'     => $request->status,
            'admin_note' => $request->admin_note,
        ]);
        return back()->with('success', 'Status pesanan diperbarui.');
    }

    // ─────── Helpers ─────────────────────────────────────────────────

    private function validateMerchandise(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'category'              => 'required|string|in:' . implode(',', array_keys(Merchandise::getCategories())),
            'description'           => 'nullable|string',
            'price'                 => 'required|integer|min:0',
            'price_member'          => 'nullable|integer|min:0',
            'image'                 => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'gallery.*'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'pre_order_open_at'     => 'nullable|date',
            'pre_order_close_at'    => 'nullable|date|after_or_equal:pre_order_open_at',
            'estimated_delivery_at' => 'nullable|date',
            'stock'                 => 'required|integer|min:0',
            'min_order'             => 'required|integer|min:1',
            'whatsapp_contact'      => 'nullable|string|max:20',
            'sort_order'            => 'nullable|integer|min:0',
            'sizes'                 => 'nullable|string',
            'colors'                => 'nullable|string',
        ]);

        $data['is_active']    = $request->boolean('is_active');
        $data['is_pre_order'] = $request->boolean('is_pre_order');

        return $data;
    }

    private function handleMainImage(Request $request, array $data, ?Merchandise $existing = null): array
    {
        if ($request->hasFile('image')) {
            if ($existing && $existing->image) {
                Storage::disk('public')->delete(ltrim(str_replace('/storage/', '', $existing->image), '/'));
            }
            $path = $request->file('image')->store('merchandise', 'public');
            $data['image'] = '/storage/' . $path;
        }
        // Remove 'gallery' key from data array (handled separately)
        unset($data['gallery']);
        return $data;
    }

    private function storeGalleryImages(Request $request, Merchandise $merchandise): void
    {
        $existing = $merchandise->images ?? [];
        foreach ($request->file('gallery') as $file) {
            $path      = $file->store('merchandise/gallery', 'public');
            $existing[] = '/storage/' . $path;
        }
        $merchandise->update(['images' => array_values($existing)]);
    }
}
