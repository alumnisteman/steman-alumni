<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuccessStoryController extends Controller
{
    public function index()
    {
        $stories = SuccessStory::orderBy('order')->latest()->get();
        return view('admin.success_stories.index', compact('stories'));
    }

    public function create()
    {
        $alumni = User::where('role', 'alumni')->orderBy('name')->get();
        return view('admin.success_stories.create', compact('alumni'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'major_year' => 'required|string|max:255',
            'quote' => 'required|string',
            'content' => 'required|string',
            'image_path' => 'nullable|image|max:2048',
            'user_id' => 'nullable|exists:users,id',
            'is_published' => 'boolean',
            'order' => 'integer'
        ]);

        if ($request->hasFile('image_path')) {
            $validated['image_path'] = $request->file('image_path')->store('success_stories', 'public');
        }

        SuccessStory::create($validated);
        \Illuminate\Support\Facades\Cache::forget('welcome_data');

        return redirect()->route('admin.success-stories.index')->with('success', 'Kisah Sukses berhasil ditambahkan.');
    }

    public function edit(SuccessStory $successStory)
    {
        $alumni = User::where('role', 'alumni')->orderBy('name')->get();
        return view('admin.success_stories.edit', compact('successStory', 'alumni'));
    }

    public function update(Request $request, SuccessStory $successStory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'major_year' => 'required|string|max:255',
            'quote' => 'required|string',
            'content' => 'required|string',
            'image_path' => 'nullable|image|max:2048',
            'user_id' => 'nullable|exists:users,id',
            'is_published' => 'boolean',
            'order' => 'integer'
        ]);

        if ($request->hasFile('image_path')) {
            if ($successStory->image_path) {
                Storage::disk('public')->delete($successStory->image_path);
            }
            $validated['image_path'] = $request->file('image_path')->store('success_stories', 'public');
        }

        $successStory->update($validated);
        \Illuminate\Support\Facades\Cache::forget('welcome_data');

        return redirect()->route('admin.success-stories.index')->with('success', 'Kisah Sukses berhasil diperbarui.');
    }

    public function destroy(SuccessStory $successStory)
    {
        if ($successStory->image_path) {
            Storage::disk('public')->delete($successStory->image_path);
        }
        $successStory->delete();
        \Illuminate\Support\Facades\Cache::forget('welcome_data');

        return redirect()->route('admin.success-stories.index')->with('success', 'Kisah Sukses berhasil dihapus.');
    }
}
