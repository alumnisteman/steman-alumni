<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\ActivityLog;
use App\Jobs\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Request as RequestFacade;

class MajorController extends Controller
{
    // Admin: List
    public function index()
    {
        $majors = Major::orderBy('group')->orderBy('name')->get();
        return View::make('admin.majors.index', compact('majors'));
    }

    // Admin: Store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:majors,name',
            'group' => 'required|string',
        ]);

        $major = Major::create($request->only(['name', 'group']));

        LogActivity::dispatch(
            Auth::id(),
            'Create Major',
            'Added major: ' . $major->name,
            $request->ip(),
            $request->header('User-Agent')
        );

        return back()->with('success', 'major berhasil ditambahkan.');
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

        LogActivity::dispatch(
            Auth::id(),
            'Update Major',
            'Updated major: ' . $major->name,
            $request->ip(),
            $request->header('User-Agent')
        );

        return back()->with('success', 'major berhasil diperbarui.');
    }

    // Admin: Delete
    public function destroy(Major $major)
    {
        $name = $major->name;
        $major->delete();

        LogActivity::dispatch(
            Auth::id(),
            'Delete Major',
            'Deleted major: ' . $name,
            RequestFacade::ip(),
            RequestFacade::header('User-Agent')
        );

        return back()->with('success', 'major berhasil dihapus.');
    }
}
