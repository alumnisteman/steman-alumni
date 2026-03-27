<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MajorController extends Controller
{
    // Admin: List
    public function index()
    {
        $majors = Major::orderBy('group')->orderBy('name')->get();
        return view('admin.majors.index', compact('majors'));
    }

    // Admin: Store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:majors,name',
            'group' => 'required|string',
        ]);

        $major = Major::create($request->only(['name', 'group']));

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Create Major',
            'description' => 'Added major: ' . $major->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        return back()->with('success', 'Jurusan berhasil ditambahkan.');
    }

    // Admin: Update
    public function update(Request $request, Major $major)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:majors,name,' . $major->id,
            'group' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $major->update($request->only(['name', 'group', 'status']));

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Update Major',
            'description' => 'Updated major: ' . $major->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        return back()->with('success', 'Jurusan berhasil diperbarui.');
    }

    // Admin: Delete
    public function destroy(Major $major)
    {
        $name = $major->name;
        $major->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Delete Major',
            'description' => 'Deleted major: ' . $name,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);

        return back()->with('success', 'Jurusan berhasil dihapus.');
    }
}
