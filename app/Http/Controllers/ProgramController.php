<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProgramController extends Controller
{
    // Admin: List programs
    public function adminIndex()
    {
        $programs = Program::latest()->get();
        return view('admin.programs.index', compact('programs'));
    }

    // Admin: Create form
    public function create()
    {
        return view('admin.programs.form');
    }

    // Admin: Store
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string',
            'content' => 'required|string',
            'registration_link' => 'nullable|url|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('image')) {
            $data['image'] = Storage::url($request->file('image')->store('uploads/programs', 'public'));
        }

        $program = Program::create($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Create Program',
            'description' => 'Added program: ' . $program->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        Cache::forget('welcome_data');

        return redirect()->route('admin.programs.index')->with('success', 'Program berhasil ditambahkan.');
    }

    // Admin: Edit form
    public function edit(Program $program)
    {
        return view('admin.programs.form', compact('program'));
    }

    // Admin: Update
    public function update(Request $request, Program $program)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string',
            'content' => 'required|string',
            'registration_link' => 'nullable|url|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($program->image) {
                $oldPath = str_replace('/storage/', '', $program->image);
                Storage::disk('public')->delete($oldPath);
            }
            $data['image'] = Storage::url($request->file('image')->store('uploads/programs', 'public'));
        }

        $program->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Update Program',
            'description' => 'Updated program: ' . $program->title,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        Cache::forget('welcome_data');

        return redirect()->route('admin.programs.index')->with('success', 'Program berhasil diperbarui.');
    }

    // Admin: Delete
    public function destroy(Program $program)
    {
        if ($program->image) {
            $oldPath = str_replace('/storage/', '', $program->image);
            Storage::disk('public')->delete($oldPath);
        }
        $name = $program->title;
        $program->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Delete Program',
            'description' => 'Deleted program: ' . $name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
        Cache::forget('welcome_data');

        return back()->with('success', 'Program berhasil dihapus.');
    }

    // Frontend: List
    public function index()
    {
        $programs = Program::where('status', 'active')->get();
        return view('programs.index', compact('programs'));
    }

    // Frontend: Show
    public function show($slug)
    {
        $program = Program::where('slug', $slug)->where('status', 'active')->firstOrFail();
        return view('programs.show', compact('program'));
    }
}
